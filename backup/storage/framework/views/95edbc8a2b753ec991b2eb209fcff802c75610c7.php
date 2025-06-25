 <!-- Maps Tab -->
 <?php
     $hb837Address = $hb837->address . ', ' . $hb837->city . ', ' . $hb837->state . ' ' . $hb837->zip;
     $googleMapsUrl =
         'https://www.google.com/maps/embed/v1/place?key=' .
         env('GOOGLE_MAPS_API_KEY') .
         '&q=' .
         urlencode($hb837Address);
 ?>

 <h2><?php echo e($hb837Address); ?></h2>
 <iframe
    src="<?php echo e($googleMapsUrl); ?>"
    width="100%" height="450"
    style="border:0;" allowfullscreen="" loading="lazy">
 </iframe>
<?php /**PATH /var/www/projecttracker/resources/views/admin/hb837/partials/maps.blade.php ENDPATH**/ ?>