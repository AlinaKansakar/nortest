jQuery(document).ready(function ($) {
  $("#qobrix_login_form").submit(function (e) {
    e.preventDefault();
    $this = $(this);
    $("#js-user-login-errors").empty();
    var user_info = $this.serialize();

    $(".errors").remove();

    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: ajax_object.ajax_url,
      data: {
        action: "user_login",
        data: user_info,
      },
      beforeSend: function () {
        $("#pleaseWaitDialog").show();
      },
      success: function (response) {
        if (response.status == "success") {
          $("#login-error-msg").hide();
          $("#login-success-msg").show();
          window.location.href = response.redirectTo;
        } else {
          $("#login-success-msg").hide();
          $("#pleaseWaitDialog").hide();
          $.each(response.errors, function (key, value) {
            $("#login-error-msg").html(value).show();
          });
        }
      },
    });
  });

  $("#qobrix-forget-password-form").submit(function (e) {
    e.preventDefault();

    var user_info = $("#qobrix-forget-password-form").serialize();
    $this = $(this);

    $(".errors").remove();
    $(".success").remove();

    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: ajax_object.ajax_url,
      data: {
        action: "forget_password",
        data: user_info,
      },
      beforeSend: function () {
        $("#pleaseWaitDialog").show();
      },
      success: function (response) {
        if (response.status == "success") {
          $("#login-error-msg").hide();
          $("#login-success-msg").show();
          $("#pleaseWaitDialog").hide();
          $("#login-success-msg").html(
            "Please check your mail to get new password."
          );
          $("#forget-password-form")
            .find("input[type='submit']")
            .attr("disabled", "disabled");
        } else {
          $("#login-success-msg").hide();
          $("#pleaseWaitDialog").hide();
          $.each(response.errors, function (key, value) {
            $("#login-error-msg").html(value).show();
          });
        }
      },
    });
  });

  $("#profile_picture").change(function () {
    var file_name = $(this).val().split("\\").pop();
    $("#profile_picture_preview").text(file_name);
  });
  
  $("#qobrix_profile_update_form").submit(function (e) {
    e.preventDefault();

    $this = $(this);
    var form_data = new FormData($this[0]);
    form_data.append('action', 'user_profile');

    $(".errors").remove();
    $(".success").remove();

    jQuery.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: form_data,
      processData: false,
      contentType: false,
      beforeSend: function () {
        $("#pleaseWaitDialog").show();
      },
      success: function (response) {
        if (response.status == "success") {
          $("#profile-error-msg").hide();
          $("#profile-success-msg").show();
          $("#pleaseWaitDialog").hide();
          $("#profile-success-msg").html("Profile updated successfully.");

          window.location.href = response.redirectTo;
        } else {
          $("#profile-success-msg").hide();
          $("#pleaseWaitDialog").hide();
          $.each(response.errors, function (key, value) {
            $("#profile-error-msg").html(value).show();
          });
        }
      },
      error: function (error) {
        console.log('Error: ' + error.responseText);
      }
    });
  });

  $('#dataTable').DataTable({
    order: [], // Disables initial sorting
  });

  $(document).on("click", ".form__password-toggle", function (e) {
    e.preventDefault();
    var $button = $(this);
    var inputId = $button.attr("aria-controls");
    var $input = inputId ? $("#" + inputId) : $button.siblings(".form__control");
    var isVisible = $input.attr("type") === "text";

    if (isVisible) {
      $input.attr("type", "password");
      $button.attr("aria-pressed", "false");
      $button.attr("aria-label", "Show password");
    } else {
      $input.attr("type", "text");
      $button.attr("aria-pressed", "true");
      $button.attr("aria-label", "Hide password");
    }
  });
});
