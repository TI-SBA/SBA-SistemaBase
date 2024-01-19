<?php $width = 230; ?>
<div class="ui-widget ui-corner-top ui-chatbox" outline="0" style="right: 10px; display: block;z-index: 50000;">
  <div class="ui-widget-header ui-corner-top ui-chatbox-titlebar ui-dialog-header" unselectable="on" style="-moz-user-select: none; width: <?=$width?>px;">
    <span unselectable="on" style="-moz-user-select: none;" name="user_chat">First2 Last2</span>
    <a href="#" class="ui-corner-all ui-chatbox-icon" role="button" unselectable="on" style="-moz-user-select: none;">
      <span class="ui-icon ui-icon-closethick" unselectable="on" style="-moz-user-select: none;">cerrar</span>
    </a>
    <a href="#" class="ui-corner-all ui-chatbox-icon" role="button" unselectable="on" style="-moz-user-select: none;">
      <span class="ui-icon ui-icon-minusthick" unselectable="on" style="-moz-user-select: none;">minimizar</span>
    </a>
  </div>
  <div class="ui-widget-content ui-chatbox-content " style="display: block;">
    <div id="box2" class="ui-widget-content ui-chatbox-log" style="width: <?=$width?>px;">
    </div>
    <div class="ui-widget-content ui-chatbox-input" style="max-width: <?=$width?>px;">
      <textarea class="ui-widget-content ui-chatbox-input-box ui-corner-all" style="width: <?=($width-18)?>px;" name="msg"></textarea>
    </div>
  </div>
</div>