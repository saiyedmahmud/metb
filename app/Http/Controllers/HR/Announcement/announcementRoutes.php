<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HR\Announcement\AnnouncementController;



Route::get("/", [AnnouncementController::class, 'getAllAnnouncement']);

Route::middleware("permission:create-announcement")->post("/", [AnnouncementController::class, 'createSingleAnnouncement']);

Route::middleware("permission:readSingle-announcement")->get("/{id}", [AnnouncementController::class, 'getSingleAnnouncement']);

Route::middleware("permission:update-announcement")->put("/{id}", [AnnouncementController::class, 'updateSingleAnnouncement']);

// Route::middleware("permission:delete-announcement")->patch("/{id}", [AnnouncementController::class, 'deletedAnnouncement']);
Route::middleware("permission:delete-announcement")->delete("/{id}", [AnnouncementController::class, 'deleteAnnouncement']);
