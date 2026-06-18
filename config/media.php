<?php

return [
    'disk' => env('MEDIA_DISK', 'public'),
    'public_paths' => [
        'experience_images' => 'uploads/experiences/images',
        'experience_videos' => 'uploads/experiences/videos',
    ],
    'max_image_size' => 5 * 1024, // KB
    'max_video_size' => 50 * 1024, // KB
    'image_mimes' => ['jpg', 'jpeg', 'png', 'webp'],
    'video_mimes' => ['mp4', 'webm', 'mov'],
    'conversions' => [
        'thumb' => ['width' => 400,  'height' => 300],
        'card' => ['width' => 800,  'height' => 600],
        'hero' => ['width' => 1600, 'height' => 900],
    ],
    'max_per_experience' => 15,
    'max_videos_per_experience' => 3,
];
