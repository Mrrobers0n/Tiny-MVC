/**
 * Created by cyber01 on 17/05/2017.
 */

let Blog = {
	NewPost: {
		init: function() {
			this._setEventHandlers();
		},

		submitForm: function() {
			let form = document.getElementById('form_newblogpost');

			form.submit();
		},

		changeAction: function(value) {
			let eInput = document.getElementById('action');

			eInput.value = value;

			this.submitForm();
		},

		_setEventHandlers: function() {
			let btn = document.getElementById('save');

			btn.addEventListener('click', function (e) {
				e.preventDefault();

				Blog.NewPost.changeAction('save');
			});

			document.getElementById('return').addEventListener('click', function (e) {
				e.preventDefault();

				history.go(-1);
			});
		}
	}
};