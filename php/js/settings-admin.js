$(document).ready(function () {

	function save(app, selector) {
		const sel = $(selector)

		OC.AppConfig.setValue(app, sel.attr('name'), sel.val());
	}

	$('#describo_submit').on('click', function (event) {
		event.preventDefault();
		OC.msg.startSaving('#describo_settings .msg');

		var app = "describo"
		$(":input[type=text]").each((index, element) => {
			save(app, element)
		});

		OC.msg.finishedSaving('#describo_settings .msg', { status: 'success', data: { message: t('describo', 'Saved.') } });
	});

	$('.section .icon-info').tipsy({ gravity: 'w' });
});