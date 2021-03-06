<?php

   $format = strtolower($this->Data('_EditorInputFormat'));
   $html_toolbar = ''; // for regular text

   $css_upload_class = ($this->Data('_canUpload'))
      ? 'editor-uploads'
      : 'editor-uploads-disabled';

   $css_ismobile_class = (IsMobile())
      ? 'editor-mobile'
      : 'editor-desktop';

   $html_toolbar    = '<div class="editor editor-format-'. $format .' '. $css_upload_class .' '. $css_ismobile_class .'">';
   $html_arrow_down = '<span class="icon icon-caret-down"></span>';
   $editor_file_input_name = $this->Data('_editorFileInputName');

   foreach($this->Data('_EditorToolbar') as $button) {

      // If the type is not an array, it's a regular button (type==button)
      if (!is_array($button['type'])) {
         $html_toolbar .= Wrap('', 'span', $button['attr']);
      } else {
         // Else this button has dropdown options, so generate them
         $html_button_dropdown_options = '';

         foreach ($button['type'] as $type_key => $button_option) {

            // If any text, use it
            $action_text = (isset($button_option['text']))
                ? $button_option['text']
                : '';

            // If the dropdown child elements require a different tag,
            // specify it in the array, then grab it here, otherwise
            // use the default, being a span.
            $html_tag = (isset($button_option['html_tag']))
                ? $button_option['html_tag']
                : 'span';

            // Concatenate child elements
            if (isset($button_option['attr'])) {
               $html_button_dropdown_options .= Wrap($action_text, $html_tag, $button_option['attr']);
            }
         }

         switch ($button['action']) {

            case 'link':
               $html_toolbar .= Wrap(
                  Wrap($html_arrow_down, 'span', $button['attr']) .''.
                  '<div class="editor-insert-dialog Flyout MenuItems" data-wysihtml5-dialog="createLink">
                     <input class="InputBox editor-input-url" data-wysihtml5-dialog-field="href" value="http://" />
                      <div class="MenuButtons">
                      <input type="button" data-wysihtml5-dialog-action="save" class="Button editor-dialog-fire-close" value="'.T('OK').'"/>
                      <input type="button" data-wysihtml5-dialog-action="cancel" class="Button Cancel editor-dialog-fire-close" value="'.T('Cancel').'"/>
                      </div>
                   </div>'
                , 'div', array('class' => 'editor-dropdown editor-dropdown-link'));
               break;

            case 'image':
               $html_toolbar .= Wrap(
                  Wrap($html_arrow_down, 'span', $button['attr']) .''.
                  '<div class="editor-insert-dialog Flyout MenuItems" data-wysihtml5-dialog="insertImage">
                     <input class="InputBox editor-input-image" data-wysihtml5-dialog-field="src" value="http://">
                     <div class="MenuButtons">
                     <label class="editor-image-align">
                      Align:
                      <select data-wysihtml5-dialog-field="className">
                        <option value="">default</option>
                        <option value="wysiwyg-float-left">left</option>
                        <option value="wysiwyg-float-right">right</option>
                      </select>
                     </label>
                     <input type="button" data-wysihtml5-dialog-action="save" class="Button editor-dialog-fire-close" value="'.T('OK').'"/>
                     <input type="button" data-wysihtml5-dialog-action="cancel" class="Button Cancel editor-dialog-fire-close" value="'.T('Cancel').'"/>
                     </div>
                  </div>'
                , 'div', array('class' => 'editor-dropdown editor-dropdown-image'));
               break;

            case 'upload':
               $html_toolbar .= Wrap(
                  Wrap($html_arrow_down, 'span', $button['attr']) .''.
                  '<div class="editor-insert-dialog Flyout MenuItems editor-file-image">
                     <div id="drop-cue-dropdown" class="drop-section file-drop">
                        '. T('Drop image/file') .'
                     </div>
                     <div class="drop-section file-input">
                        <span class="file-or">'.T('or').'</span> <input type="file" name="'. $editor_file_input_name .'[]" multiple />
                     </div>
                     <div class="drop-section image-input" title="'. T('Paste the URL of an image to quickly embed it.') .'">
                        <input class="InputBox editor-input-image" placeholder="'. T('Image URL') .'" />
                     </div>
                  </div>'
                , 'div', array('class' => 'editor-dropdown editor-dropdown-upload'));
               break;

             case 'color':

                 $colorType = $button['type'];

                 $textColorOptions = '';
                 if (isset($colorType['text'])) {
                     foreach($colorType['text'] as $textColor) {
                         $textColorOptions .= Wrap('', $textColor['html_tag'], $textColor['attr']);
                     }

                     if ($textColorOptions) {
                         $textColorOptions = '<div class="color-group text-color ClearFix"><i class="icon icon-font" title="Text"></i>' . $textColorOptions . '</div>';
                     }
                 }

                 $highlightColorOptions = '';
                 if (isset($colorType['highlight'])) {
                     foreach($colorType['highlight'] as $highlightColor) {
                         $highlightColorOptions .= Wrap('', $highlightColor['html_tag'], $highlightColor['attr']);
                     }

                     if ($highlightColorOptions) {
                         $highlightColorOptions = '<div class="color-group highlight-color ClearFix"><i class="icon icon-sign-blank" title="Highlight"></i>' . $highlightColorOptions . '</div>';
                     }
                 }

                 $cssHasHighlight = ($highlightColorOptions)
                     ? 'color-has-highlight'
                     : '';

                 $colorOptions = $textColorOptions . $highlightColorOptions;

                 $html_toolbar .= Wrap(
                     Wrap($html_arrow_down, 'span', $button['attr']) .''.
                     Wrap($colorOptions, 'div', array('class' => 'editor-insert-dialog Flyout MenuItems', 'data-wysihtml5-dialog' => ''))
                     , 'div', array('class' => "editor-dropdown editor-dropdown-color $cssHasHighlight")
                 );
                 break;

            // All other dropdowns (color, format, emoji)
            default:
               $html_toolbar .= Wrap(
                  Wrap($html_arrow_down, 'span', $button['attr']) .''.
                  Wrap($html_button_dropdown_options, 'div', array('class' => 'editor-insert-dialog Flyout MenuItems', 'data-wysihtml5-dialog' => ''))
               , 'div', array('class' => 'editor-dropdown editor-dropdown-default editor-action-'. $button['action']));
               break;
         }
      }
   }

   $html_toolbar .= '</div>';

   // Add progress meter for file uploads.
   $html_toolbar .= '<div class="editor-upload-progress"></div>';

   // Add drop message when dragging over dropzone. Only display when
   // dragging over element.
   $html_toolbar .= '<div class="editor-upload-attention">'. T('Drop image/file') .'</div>';

   // Generate output for view
   echo $html_toolbar;

?>
