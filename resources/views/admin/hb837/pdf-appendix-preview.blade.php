<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HB 837 Appendix A Preview - {{ $hb837->property_name }}</title>
    <style>
        @page {
            margin: 40px 40px;
        }

        body {
            font-family: "Arial", "Helvetica", sans-serif;
            font-size: 11px;
            line-height: 1.25;
            color: #333;
        }

        h1,
        h2,
        h3,
        h4 {
            margin: 0;
            padding: 0;
            font-weight: bold;
        }

        .section-title {
            font-size: 13px;
            margin: 0 0 8px 0;
        }

        .small {
            font-size: 10px;
            color: #6c757d;
        }

        .img-placeholder {
            border: 1px solid #dee2e6;
            padding: 18px;
            text-align: center;
            margin: 6px 0 10px 0;
            page-break-inside: avoid;
        }

        .img-placeholder .ph-title {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .avoid-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    @php
        $filesByPosition = ($hb837->relationLoaded('files') ? $hb837->files : $hb837->files()->get())
            ->whereNotNull('file_position')
            ->keyBy('file_position');

        $appendixMap = $filesByPosition->get('appendix_a_map');
        $appendixPhoto1 = $filesByPosition->get('appendix_a_photo_1');
        $appendixPhoto2 = $filesByPosition->get('appendix_a_photo_2');
        $appendixPhoto3 = $filesByPosition->get('appendix_a_photo_3');

        $fileImgSrc = function ($file) {
            if (!$file || empty($file->file_path)) {
                return null;
            }

            $relative = ltrim((string) $file->file_path, '/');

            // Prefer the real storage path (works even if public/storage symlink is missing).
            $fsPath = storage_path('app/public/' . $relative);
            if (!is_file($fsPath)) {
                $fsPath = public_path('storage/' . $relative);
            }

            if (!is_file($fsPath)) {
                return null;
            }

            // Most reliable approach for DomPDF across environments: embed local images
            // as base64 data URIs. This avoids Windows file:// quirks and chroot issues.
            try {
                $bytes = @file_get_contents($fsPath);
                if ($bytes !== false && $bytes !== '') {
                    $mime = null;

                    if (function_exists('mime_content_type')) {
                        $mime = @mime_content_type($fsPath) ?: null;
                    }

                    if (!$mime) {
                        $ext = strtolower(pathinfo($fsPath, PATHINFO_EXTENSION));
                        $mime = match ($ext) {
                            'jpg', 'jpeg' => 'image/jpeg',
                            'png' => 'image/png',
                            'gif' => 'image/gif',
                            'webp' => 'image/webp',
                            default => 'image/jpeg',
                        };
                    }

                    return 'data:' . $mime . ';base64,' . base64_encode($bytes);
                }
            } catch (\Throwable $e) {
                // Fall through to file path attempt.
            }

            // DomPDF on Windows is most reliable with a file:// URI, but filenames
            // with spaces must be URL-encoded.
            $path = str_replace('\\', '/', $fsPath);

            if (preg_match('/^[A-Za-z]:\//', $path)) {
                // Convert to file:///C:/... and encode segments after the drive.
                $drivePrefix = substr($path, 0, 3); // e.g. C:/
                $rest = substr($path, 3);
                $rest = ltrim($rest, '/');
                $segments = $rest === '' ? [] : explode('/', $rest);
                $encodedRest = implode('/', array_map('rawurlencode', $segments));

                return 'file:///' . $drivePrefix . $encodedRest;
            }

            // Non-Windows absolute path: encode each segment.
            $segments = explode('/', ltrim($path, '/'));
            return '/' . implode('/', array_map('rawurlencode', $segments));
        };
    @endphp

    <h2 class="section-title">Appendix A. Photos (Preview)</h2>
    <div class="small" style="margin-bottom: 10px;">
        {{ $hb837->property_name }} &nbsp;|&nbsp; Generated {{ $generated_at ?? '' }}
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 34%; vertical-align: top; padding-right: 10px;">
                <div class="small" style="line-height: 1.35;">
                    <div style="font-weight: bold; margin-bottom: 6px;">Map Notes</div>
                    @if ($appendixMap && $appendixMap->description)
                        {!! nl2br(e($appendixMap->description)) !!}
                    @else
                        <span class="small">(Optional) Add notes/labels in the file description for the map upload.</span>
                    @endif
                </div>
            </td>
            <td style="width: 66%; vertical-align: top;">
                @php $mapSrc = $fileImgSrc($appendixMap); @endphp
                @if ($mapSrc)
                    <img src="{{ $mapSrc }}" alt="Appendix map" style="width: 100%; height: auto; border: 1px solid #999;">
                @else
                    <div class="img-placeholder" style="height: 320px;">
                        <div class="ph-title">[MAP IMAGE – upload with position Appendix A Map]</div>
                        <div class="small">Position: appendix_a_map</div>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 14px;">
        <tr>
            @foreach ([$appendixPhoto1, $appendixPhoto2, $appendixPhoto3] as $i => $photo)
                <td style="width: 33.33%; vertical-align: top; padding-right: {{ $i < 2 ? '10px' : '0' }};">
                    @php $photoSrc = $fileImgSrc($photo); @endphp
                    @if ($photoSrc)
                        <img src="{{ $photoSrc }}" alt="Appendix photo {{ $i + 1 }}" style="width: 100%; height: 130px; object-fit: cover; border: 1px solid #999;">
                    @else
                        <div class="img-placeholder" style="height: 130px;">
                            <div class="ph-title">[PHOTO {{ $i + 1 }} – upload with position]</div>
                            <div class="small">Position: appendix_a_photo_{{ $i + 1 }}</div>
                        </div>
                    @endif

                    @if ($photo && $photo->description)
                        <div class="small" style="margin-top: 6px;">{!! nl2br(e($photo->description)) !!}</div>
                    @endif
                </td>
            @endforeach
        </tr>
    </table>
</body>

</html>
