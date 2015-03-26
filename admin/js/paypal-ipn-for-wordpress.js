jQuery(function ($) {
'use strict';
if ($('input[name="business"]').length > 0) {
	if ($('input[name="bn"]').length > 0) {
		$('input[name="bn"]').val("AngellEYE_PHPClass");
	} else {
		$('input[name="business"]').after("<input type='hidden' name='bn' value='AngellEYE_PHPClass' />");
	}
}

})(jQuery);