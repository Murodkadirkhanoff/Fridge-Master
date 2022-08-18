<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\LocationResource;
use App\Models\Block;
use App\Models\Booking;
use App\Models\Freezer;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Psy\Util\Str;

class BookingController extends Controller
{
    const COST_PER_DAY = 500; // сумма за сутки хранения 500$


    /**
     * @OA\Get (
     * path="/api/locations",
     * operationId="Locations List",
     * tags={"Base APIs"},
     * summary="Locations List",
     * description="Get Locations List with freezers and blocks",
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent()
     *       )
     * )
     */
    public function locations(): \Illuminate\Http\JsonResponse
    {
        $data = LocationResource::collection(Location::with('freezers')->get());

        return $this->success($data);
    }


    /**
     * @OA\Post  (
     * path="/api/calculate",
     * operationId="Calculate the amount of booking",
     * tags={"Base APIs"},
     * summary="Locations List",
     * description="Calculate the amount of booking",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"location_id","product_volume", "temperature", "date_from", "date_to"},
     *               @OA\Property(property="location_id", type="number", description="ID местоположения <strong> (из /api/locations ) </strong>"),
     *               @OA\Property(property="product_volume", type="number", description="Введите объем продукции"),
     *               @OA\Property(property="temperature", type="number", description="Введите температура для хранения продуктов"),
     *               @OA\Property(property="date_from", type="string", description="Дата начала бронирования"),
     *               @OA\Property(property="date_to", type="string",description="Дата окончания бронирования"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent()
     *       )
     * )
     */
    public function calculate(BookingRequest $request): \Illuminate\Http\JsonResponse
    {
        $freezer_ids = Freezer::where('location_id', $request->location_id)->pluck('id');
        $available_blocks_count = Block::whereIn('freezer_id', $freezer_ids)->empty()->count();
        $blocks_count_to_book = ceil($request->product_volume / 2); // Делим введенный пользователем объем товара (в м3) на 2 м3 ( 2 * 1 * 1 - объем 1 блока)


        $data = [
            'blocks_to_booking' => $blocks_count_to_book,
            'total_amount' => $blocks_count_to_book * 1000,
            'is_available' => $available_blocks_count >= $blocks_count_to_book
        ];
        return $this->success($data);
    }


    /**
     * @OA\Post  (
     * path="/api/booking",
     * operationId="Booking blocks",
     * tags={"Base APIs"},
     * summary="Booking blocks",
     * description="Booking blocks",
     * @OA\SecurityScheme(
     *    securityScheme="sanctum",
     *    in="header",
     *    name="bearerAuth",
     *    type="http",
     *    scheme="bearer",
     *    bearerFormat="JWT",
     * ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"location_id","product_volume", "temperature", "date_from", "date_to"},
     *               @OA\Property(property="location_id", type="number", description="ID местоположения <strong> (из /api/locations ) </strong>"),
     *               @OA\Property(property="product_volume", type="number", description="Введите объем продукции"),
     *               @OA\Property(property="temperature", type="number", description="Введите температура для хранения продуктов"),
     *               @OA\Property(property="date_from", type="string", description="Дата начала бронирования"),
     *               @OA\Property(property="date_to", type="string",description="Дата окончания бронирования"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent()
     *       )
     * )
     */
    public function booking(BookingRequest $request): \Illuminate\Http\JsonResponse
    {
        $date_from = Carbon::parse($request->date_from);
        $date_to = Carbon::parse($request->date_to);

        $diff_day = $date_from->diffInDays($date_to);

        if ($date_from->gt($date_to) || $diff_day > 24) {
            return $this->error('срок хранения не может превышать 24 часов или дата начала не может быть больше даты окончания ', 422);
        }

        $freezer_ids = Freezer::where('location_id', $request->location_id)->pluck('id');
        $available_blocks_count = Block::whereIn('freezer_id', $freezer_ids)->empty()->count();
        $blocks_count_to_book = ceil($request->product_volume / 2);

        if ($available_blocks_count >= $blocks_count_to_book) {
            $booking = Booking::create([
                'user_id' => 1,
                'total_amount' => $blocks_count_to_book * self::COST_PER_DAY,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'access_code' => \Illuminate\Support\Str::random(12),
                'product_volume' => $request->product_volume
            ]);

            $blocks = Block::whereIn('freezer_id', $freezer_ids)->empty()->take($blocks_count_to_book)->get();
            foreach ($blocks as $block) {
                $block->is_empty = 0;
                $block->save();
                $booking->blocks()->attach($block->id);
            }
            $data = [
                'message' => 'Бронирование успешно принято'
            ];
            return $this->success($data);
        } else {
            $result = 'Свободное пространство недоступно';
            $data = [
                'message' => $result
            ];
            return $this->error($data, 403);
        }
    }


    /**
     * @OA\Get (
     * path="/api/my-bookings",
     * operationId="List of specific user bookings",
     * tags={"Base APIs"},
     * summary="Locations List of specific user bookings",
     * description="Get List of specific user bookings",
     * @OA\SecurityScheme(
     *    securityScheme="sanctum",
     *    in="header",
     *    name="bearerAuth",
     *    type="http",
     *    scheme="bearer",
     *    bearerFormat="JWT",
     * ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent()
     *       )
     * )
     */
    public function myBookings(): \Illuminate\Http\JsonResponse
    {
        $bookings = BookingResource::collection(Booking::where('user_id', 1)->with('blocks')->get());

        return $this->success($bookings);
    }


}
