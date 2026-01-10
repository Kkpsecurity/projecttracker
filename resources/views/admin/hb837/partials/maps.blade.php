 <!-- Maps Tab -->
 @php
     $hb837Address = $hb837->address . ', ' . $hb837->city . ', ' . $hb837->state . ' ' . $hb837->zip;
     $googleMapsUrl =
         'https://www.google.com/maps/embed/v1/place?key=' .
         env('GOOGLE_MAPS_API_KEY') .
         '&q=' .
         urlencode($hb837Address);
 @endphp

 <h2>{{ $hb837Address }}</h2>
 <iframe
    src="{{ $googleMapsUrl }}"
    width="100%" height="450"
    style="border:0;" allowfullscreen="" loading="lazy">
 </iframe>
