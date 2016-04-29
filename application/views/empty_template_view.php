<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <?php print $tags; ?>
    <link rel="stylesheet" href="/css/main/style.min.css?v=4" charset="utf-8">
  </head>
  <header>
    <div style="width: 145px; margin: 20px auto;">
      <a class="home-link" href="/" alt="Correctarium" title="Correctarium"></a>
    </div>
  </header>
  <?php include 'application/views/'.$content_view; ?>
</html>
