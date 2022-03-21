import tinymce from 'tinymce';
var useDarkMode = true;
tinymce.init({
  selector: 'textarea',
  plugins: 'preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars emoticons',
  menubar: 'file edit view insert format tools table help',
  toolbar: 'undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify |fullscreen preview code',
  toolbar_sticky: true,
  autosave_ask_before_unload: true,
  autosave_interval: '30s',
  autosave_prefix: '{path}{query}-{id}-',
  autosave_restore_when_empty: false,
  autosave_retention: '2m',
  image_advtab: true,
  importcss_append: true,
  height: 600,
  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
  toolbar_mode: 'sliding',
  contextmenu: 'link',
  skin: useDarkMode ? 'oxide-dark' : 'oxide',
  content_css: useDarkMode ? 'dark' : 'default',
 });