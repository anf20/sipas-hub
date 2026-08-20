<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HandlesImageUploads
{
    /**
     * Compress and convert uploaded image to WebP format, optionally resizing large dimensions.
     * Falls back to normal upload if GD extension is not available.
     */
    protected function convertToWebp($uploadedFile, int $maxDimension = 1600): string
    {
        try {
            if (function_exists('imagewebp')) {
                $path = $uploadedFile->getRealPath();

                // Determine image type and create image resource
                $image = match (strtolower($uploadedFile->getClientOriginalExtension())) {
                    'jpg', 'jpeg' => @imagecreatefromjpeg($path),
                    'png' => @imagecreatefrompng($path),
                    'webp' => @imagecreatefromwebp($path),
                    'gif' => @imagecreatefromgif($path),
                    default => false,
                };

                if ($image !== false) {
                    $origWidth = imagesx($image);
                    $origHeight = imagesy($image);

                    // Resize dimension dynamically if it exceeds maxDimension limit
                    if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
                        if ($origWidth > $origHeight) {
                            $width = $maxDimension;
                            $height = (int) (($origHeight / $origWidth) * $maxDimension);
                        } else {
                            $height = $maxDimension;
                            $width = (int) (($origWidth / $origHeight) * $maxDimension);
                        }

                        $newImage = imagecreatetruecolor($width, $height);

                        // Preserve transparency for PNG / WebP
                        if (in_array(strtolower($uploadedFile->getClientOriginalExtension()), ['png', 'webp'])) {
                            imagealphablending($newImage, false);
                            imagesavealpha($newImage, true);
                        }

                        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);
                        imagedestroy($image);
                        $image = $newImage;
                    }

                    // Ensure transparency works correctly when exporting to WebP
                    imagealphablending($image, false);
                    imagesavealpha($image, true);

                    // Capture WebP output using output buffering
                    ob_start();
                    if (imagewebp($image, null, 75)) {
                        $webpData = ob_get_clean();
                        imagedestroy($image);

                        $filename = 'payment-proofs/'.uniqid().'.webp';
                        Storage::disk('public')->put($filename, $webpData);

                        return $filename;
                    }
                    ob_end_clean();
                    imagedestroy($image);
                }
            }
        } catch (\Exception $e) {
            Log::error('WebP Compression & Resizing Failed, falling back: '.$e->getMessage());
        }

        // Fallback to standard Laravel store
        return $uploadedFile->store('payment-proofs', 'public');
    }
}
