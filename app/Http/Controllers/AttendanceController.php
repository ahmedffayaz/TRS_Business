<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Http\Requests\AttendanceRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param AttendanceRequest $request
     * @param Attendance $attendance
     *
     * @return JsonResponse
     */
 
    // public function store(AttendanceRequest $request, Attendance $attendance)
    // {
        
    //     $action = 'created';
    //     $inputs = $request->all();
    //     $inputs['user_id'] = $request->has('user_id') ? $request->get('user_id') : $this->auth_user->id;
    //     $found = Attendance::where('user_id', $inputs['user_id'])
    //         ->where('id', '!=', $inputs['attendance_id'])
    //         ->where(function ($query) use ($inputs) {
    //             $query->whereBetween('start_date', [$inputs['start_date'], $inputs['end_date']])
    //                 ->orWhereBetween('end_date', [$inputs['start_date'], $inputs['end_date']]);
    //         })->first();

    //     if ($found) {
    //         return response()->json([
    //             'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
    //             'message' => 'Attendance already exists between these dates',
    //         ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    //     } else {
    //         if (!empty($inputs['attendance_id'])) {
    //             $attendance = Attendance::findOrFail($inputs['attendance_id']);
    //             $attendance->update($inputs);
    //             $action = 'updated';
    //         } else {
    //             $attendance = $attendance->create($inputs);
    //         }
    //         $days = dateDiff($attendance->end_date, $attendance->start_date);
    //         $end_date = $attendance->end_date;
    //         if ($days > 0) {
    //             $end_date = date('Y-m-d', strtotime($attendance->end_date . ' + 1 day'));
    //         }
    //         $event = [
    //             'start' => $attendance->start_date,
    //             'end' => $end_date,
    //             'title' => ($attendance->is_working == '1' ? 'Work from home' : 'Leave')
    //             . '<br/>' . $attendance->user()->first()->name . '<br/>Days: ' . (($days === 0) ? 1 : $days),
    //             'editable' => true,
    //             'user_id' => $inputs['user_id'],
    //             'auth_user_id' => $this->auth_user->id,
    //             'color' => $attendance->is_working == '1' ? '#38c172' : '#e3342f',
    //             'evt' => $attendance,
    //         ];
    //         return response()->json([
    //             'status' => JsonResponse::HTTP_OK,
    //             'message' => $action === 'created'
    //             ? 'Attendance marked successfully!' : 'Attendance updated successfully',
    //             'event' => $event,
    //         ], JsonResponse::HTTP_OK);
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    // public function destroy(int $id)
    // {
    //     try {
    //         $attendance = Attendance::findOrFail($id);
    //         $attendance->delete();
    //     } catch (ModelNotFoundException $exception) {
    //         return response()->json([
    //             'status' => JsonResponse::HTTP_NOT_FOUND,
    //             'message' => 'Attendance record does not exists!',
    //         ], JsonResponse::HTTP_NOT_FOUND);
    //     } catch (Exception $exception) {
    //         return response()->json([
    //             'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
    //             'message' => $exception->getMessage(),
    //         ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    //     return response()->json([
    //         'status' => JsonResponse::HTTP_OK,
    //         'message' => 'Attendance record deleted successfully',
    //     ], JsonResponse::HTTP_OK);
    // }
}