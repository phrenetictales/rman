{{#release}}
  <script src="/js/vendor/jquery.ui.widget.js"></script>
  <!--
  The Load Image plugin is included for the preview images and image resizing functionality ...
  (NOT INCLUDED IN THE ZIP FILE, FFS!)
  <script src="http://blueimp.github.io/JavaScript-Load-Image/js/load-image.min.js"></script>
  -->
  <!-- The Canvas to Blob plugin is included for image resizing functionality -->
  <!-- NOT IN PACKAGE
  <script src="http://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
  -->
  <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
  <script src="/js/jquery.iframe-transport.js"></script>
  <!-- The basic File Upload plugin -->
  <script src="/js/jquery.fileupload.js"></script>
  <!-- The File Upload processing plugin -->
  <!-- <script src="/js/jquery.fileupload-process.js"></script> -->
  <!-- The File Upload image preview & resize plugin -->
  <!-- <script src="/js/jquery.fileupload-image.js"></script> -->
  <!-- The File Upload validation plugin -->
  <!-- <script src="/js/jquery.fileupload-validate.js"></script> -->
  <p>
    <label for="picture">Photo</label>
    <br/>
    {{#picture}}
      <img src="/img/profile_person_generic.jpg"/>
    {{/picture}}
    <br/>
    <input id="picture" type="file" name="picture" data-url="/pictures/upload/"/>
    <input type="hidden" name="picture_id"/>
  </p>
  <!-- Borrow the Artist picture code for now -->
  <script src="/js/artists/picture.js"></script>
  <p>
    <label for="title">Name</label>
    <br/>
    <input name="title" value="{{title}}"/>
  </p>
  <p>
    <label for="tracks">Tracks</label>
    <br/>
    <ul id="tracks"></ul>
    <button id="addtrack">Add Track</button>
  </p>
  <div style="display:hidden" id="addtrack-dialog">
    <label for="track-title">Title</label>
    <br/>
    <input type="text" name="track-title" id="track-title">
    <br/>
    <label for="track-artists">Artist</label>
    <input type="text" name="track-artists" id="track-artists">
    <br/>
    <button id="track-add">Add Track</button>
  </div>
  <link rel="stylesheet" type="text/css" href="/css/jquery.tagit.css"></link>
  <script src="/js/tag-it.js"></script>
  <script>var artists = {{{tags}}};</script>
  <script src="/js/releases/tracks.js"></script>
{{/release}}
