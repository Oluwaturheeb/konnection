(function ($) {
	function empty(handler) {
		var v = $(handler).val();

		if (v == null || v == undefined) {
			$(handler).focusin();
			return true;
		} else if (v.trim().length == 0) {
			return true;
		} else {
			return false;
		}
	}

	function redirect(loc = '') {
		if (loc === '') {
			setTimeout(() => {
				location.reload();
			}, 1000);
		} else {
			setTimeout(() => {
				location = loc + '.php';
			}, 1000);
		}
	}

	var load =
		'\
<div class="loader">\
    <div class="loading"></div>\
    <div class="loading"></div>\
    <div class="loading"></div>\
</div>';
	var roller = '<div class="roll"></div>';

	//form-control
	$(".form-control").on({
		mouseover: function () {
			$(this)
				.prev("label")
				.slideDown(500);
		},
		keyup: function () {
			$(this)
				.prev("label")
				.slideDown(500);
		}
	});

	//search

	$(".search-btn").click(function (e) {
		e.preventDefault();

		$(".hidden-sm-up").hide();
		$("#search-field").show();
	});

	$("#lookup .close").click(function (e) {
		$("#search-field").hide();
		$(".hidden-sm-up").show();
	});

	// ajaxsetup

	$.ajaxSetup({
		type: "post",
		url: "request.php"
	});

	//sm-links
	$(".button").click(function (x) {
		if ($(this).hasClass("open")) {
			$(".sm-links").css({
				"margin-right": "-50rem"
			});
			$(".global").hide();
			$(this).removeClass("open");
		} else {
			$(".sm-links").css({
				"margin-right": "0"
			});
			$(this).addClass("open");
			$(".global").show();
		}
	});

	$(".global").click(() => {
		$(".sm-links").css({
			"margin-right": "-50rem"
		});
		$(".global").hide();
		$(this).removeClass("open");
	});

	//reg step_1
	$(".register .first").submit(function (e) {
		e.preventDefault();

		if (empty("#first")) {
			$(".register-info").html("Name field cannot be blank!");
		} else if (empty("#last")) {
			$(".register-info").html("Name field cannot be blank!");
		} else if (empty("#gender")) {
			$(".register-info").html("Select your gender!");
		} else if (empty("#dob")) {
			$(".register-info").html("Pick your date of birth!");
		} else if (empty("#interest")) {
			$(".register-info").html("Let' know your interest!");
		} else {
			$.ajax({
				data: $(this).serialize(),
				beforeSend: () => {
					$(".register-info").html(load);
				},
				success: e => {
					if (e == "ok") {
						$(".steps span:first")
							.removeClass("active")
							.next("span")
							.addClass("active");
						$(this)
							.hide()
							.next("form")
							.css({
								display: "grid"
							});
						$(".register-info").empty();
					} else {
						$(".register-info").html(e);
					}
				}
			});
		}
	});

	//step_2

	$("#img").submit(function (x) {
		x.preventDefault();

		$.ajax({
			data: new FormData(this),
			beforeSend: () => {
				$(".register-info").html(load);
			},
			success: e => {
				if (e == "ok") {
					$(".register-info").html('');
					$(".steps span:last")
						.addClass("active")
						.siblings()
						.removeClass("active");
					$(".register form:last")
						.show()
						.siblings("form")
						.hide();
				} else {
					$(".register-info").html(e);
				}
			},
			cache: false,
			processData: false,
			contentType: false
		});
	});

	//step_3

	var store = localStorage;

	$("#third").submit(function (e) {
		e.preventDefault();

		if (empty("#email")) {
			$(".register-info").html("Email field cannot be empty");
		} else if (empty("#password")) {
			$(".register-info").html("Email field cannot be empty");
		} else {
			$.ajax({
				data: $(this).serialize(),
				beforeSend: () => {
					$(".register-info").html(load);
				},
				success: e => {
					if (e == "ok") {
						store.setItem('login', $(this).serialize[0]);
						$(".register-info").html("Registration complete!");
						redirect('profile');
					} else {
						$(".register-info").html(e);
					}
				}
			});
		}
	});

	//login

	$("#login").submit(function (e) {
		e.preventDefault();

		if (empty("#email")) {
			$(".login-info").html("Email field cannot be empty!");
		} else if (empty("#password")) {
			$(".login-info").html("Password field cannot be empty!");
		} else {
			$.ajax({
				data: $(this).serialize(),
				beforeSend: () => {
					$(".login-info").html(load);
				},
				success: e => {
					if (e == "ok") {
						$(".login-info").html('Login successful!');
						store.setItem('login', $('#email').val());
						redirect('index');
					} else {
						$(".login-info").html(
							"Credentials does not match any account, try again!"
						);
					}
				}
			});
		}
	});

	//    version 2

	var active = store.getItem('login');
	var log = $('.active-log');

	if (active == '' || active == null || active == 'undefined') {
		log.html(' <label>Email</label><input type="email" name="login" id="email" class="form-control" placeholder="Enter your email address...">');
	} else {
		log.html('<h3>' + active + '</h3><input type="hidden" name="login" id="email" value="' + active + '">');
		$('#notmyacc').html('Not your account. Click <a href="/" class="notmyacc">here</a>.');
	}

	$('.notmyacc').click((e) => {
		e.preventDefault();

		store.removeItem('login');
		redirect();
	})

	$(".tab ul li").click(function () {
		var id = $(this).attr('id');
		$(this).addClass("active").siblings().removeClass("active");
		var to = $(this).attr("id");
		$('.tab-item .' + id).show().siblings().hide();
	});

	$("#change-pic").change(function () {
		var chk = confirm(
			"You're about to change your profile picture, do you want to continue!"
		);

		if (chk) {
			$.ajax({
				data: new FormData(this),
				success: e => {
					if (e == "ok") {
						alert("picture changed successfully");
					} else {
						alert(e);
					}
				},
				cache: false,
				processData: false,
				contentType: false
			});
		}
	});

	$("#editing").submit(function (e) {
		e.preventDefault();

		var info = $(".edit-info");

		if (empty("#firstname")) {
			info.html("Enter your name!");
		} else if (empty("#lastname")) {
			info.html("Enter your name!");
		} else if (empty("#about")) {
			info.html("Enter some text!");
		} else if (empty("#phone")) {
			info.html("Enter your phone number!");
		} else if (empty("#interest")) {
			info.html("Enter your interest!");
		} else if (empty("#dob")) {
			info.html("Select your date of birth!");
		} else if (empty("#addr")) {
			info.html("Enter your address!");
		} else {
			$.ajax({
				data: $(this).serialize(),
				beforeSend: () => {
					info.html(load);
				},
				success: e => {
					if (e == "ok") {
						info.html("Profile updated successfully!");
						redirect();
					} else {
						info.html(e);
					}
				}
			});
		}
	});

	$("#credit").submit(function (e) {
		e.preventDefault();

		if (empty("#email")) {
			$(".credit-info").html("Email field cannot be empty!");
		} else if (empty("#password")) {
			$(".credit-info").html("Password field cannot be empty!");
		} else {
			$.ajax({
				data: $(this).serialize(),
				beforeSend: () => {
					$(".credit-info").html(load);
				},
				success: e => {
					if (e == "ok") {
						$(".credit-info").html('Login successful!');
						store.setItem('login', $('#email').val());
						redirect();
					} else {
						$(".credit-info").html(e);
					}
				}
			});
		}
	});

	$("#chat").submit(function (e) {
		e.preventDefault();

		if (!empty("#msg")) {
			$.ajax({
				data: $(this).serialize(),
				beforeSend: () => {
					$(".cmsg-info").html(load);
				},
				success: e => {
					$("#msg").val('');
					$(".cmsg-info").html("");
				}
			});
		}
	});

	$('a.attachment').click((e) => {
		e.preventDefault();

		$('#attachment').css({
			'display': 'grid'
		});
	});

	$('#sub-attachment').submit(function (e) {
		e.preventDefault();
		var info = $('.attachment-info');

		$.ajax({
			data: new FormData(this),
			beforeSend: () => {
				info.html(load);
			},
			success: e => {alert(e);
				if (e == "ok") {
					info.html("file uploaded successfully");
					redirect();
				} else {
					info.html(e);
				}
			},
			cache: false,
			processData: false,
			contentType: false
		});
	});

	$('#sub-attachment .close').click(function () {
		$(this).parents('#attachment').css({
			'display': 'none'
		});
	})

	$(".add").click(() => {
		$(".create-album")
			.show()
			.siblings()
			.hide();
	});

	$("#create-album").submit(function (e) {
		e.preventDefault();
		var info = $(".album-info");
		if (empty("#album-name")) {
			info.html("Enter some text!");
		} else {
			$.ajax({
				data: $(this).serialize(),
				beforeSend: () => {
					info.html(load);
				},
				success: e => {
					if (e == "ok") {
						info.html("Album create successfully!");
						redirect();
					} else {
						info.html(e);
					}
				}
			});
		}
	});

	$(".upload-pic").click(() => {
		$("#upload")
			.show()
			.siblings()
			.hide();
	});

	$("#upload").submit(function (e) {
		e.preventDefault();
		var info = $(".upload-info");
		$.ajax({
			data: new FormData(this),
			beforeSend: () => {
				info.html(load);
			},
			success: e => {
				if (e == "ok") {
					info.html("Photo uploaded successfully!");
					redirect()
				} else {
					info.html(e);
				}
			},
			cache: false,
			processData: false,
			contentType: false
		});
	});

	$(".add-field").click(() => {
		var field = '\
        <div class="fields">\
            <hr>\
            <div class="form-group">\
                <div class="placeholder">\
                    <input name="files[]" class="picture" type="file">\
                    <span>Select picture</span>\
                </div>\
            </div>\
            <div class="form-group">\
                <label for="">Caption</label>\
                <textarea name="caption[]" id="" class="form-control" rows="10" cols="8" placeholder="Enter caption for this image!"></textarea>\
            </div>\
        </div>\
      ';


		var cont = $('.fields').html();
		var chk = 0;
		$('#upload .fields').each((a, b) => {
			chk = a;
		});
		if (chk > 3) {
			$('.upload-info').html('Maximum uplod limit reached!');
		} else if (chk < 4) {
			$('.fields').html(cont + field);
		}
	});

	$('.menu').click(function (e) {
		$(this).next('.dot-links').slideToggle();
	});

	$('.dot-links a, .dot-links li').click(function (e) {
		e.preventDefault();
		var id = $(this).attr('id');
		var act = $(this).attr('name');
		if (id !== '') {
			if (act == 'dp') {
				$.ajax({
					data: 'dp=' + id,
					success: e => {
						if (e == 'ok') {
							alert('Success');
						} else {
							alert(e)
						}
					}
				});
			} else if (act == 'thumb') {
				$.ajax({
					data: 'thumb=' + id,
					success: e => {
						if (e == 'ok') {
							alert('Success');
						} else {
							alert(e)
						}
					}
				});
			} else if (act == 'del') {
				$.ajax({
					data: 'del=' + id,
					success: e => {
						if (e == 'ok') {
							alert('Success');
						} else {
							alert(e)
						}
					}
				});
			} else if (act == 'rmalbum') {
				$.ajax({
					data: 'rmalbum=' + id,
					success: e => {
						if (e == 'ok') {
							alert('Success');
						} else {
							alert(e)
						}
					}
				});
			}
		}
	});

	$('.switch').click(function (e) {
		e.preventDefault();

		if ($('.img-slide').hasClass('touch')) {
			$('.img-slide').removeClass('touch');
			$('.img-slide-alt').addClass('touch');


		} else {
			$('.img-slide').addClass('touch');
			$('.img-slide-alt').removeClass('touch');
		}
	});

	$('a.btn').click(function (e) {
		e.preventDefault();

		var id = $(this).attr('id');

		if (id) {
			$.ajax({
				beforeSend: () => {
					$(this).html(roller);
				},
				data: 'konnect=' + id,
				success: e => {
					if (e == 'ok') {
						$(this).html('Sent');
					} else {
						$(this).html(e)
					}
				}
			});
		}
	});

	$('.text span').click(function (e) {
		var id = $(this).attr('id');


		if (id) {
			if ($(this).hasClass('yes')) {
				var data = 'konnection=yes&id=' + id;
				var res = 'Konnected';
			} else {
				data = 'konnection=no&id=' + id;
				res = 'Refused';
			}
			
			$.ajax({
				beforeSend: () => {
					$(this).html(roller);
				},
				data: data,
				success: e => {
					if (e == 'ok') {
						$(this).html(res);
					}
				}
			});
		}
	})
})(jQuery);