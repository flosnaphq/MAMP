<script src="https://cdn.omise.co/omise.js"></script>
<script>
  Omise.setPublicKey("pkey_test_53q8mnpryrjjjc53baa");
</script>


<form action="/guest-user/checkout" method="post" id="checkout">
  <div id="token_errors"></div>

  <input type="hidden" name="omise_token">

  <div>
    Name<br>
    <input type="text" data-omise="holder_name" value="tests">
  </div>
  <div>
    Number<br>
    <input type="text" data-omise="number" value="4242424242424242">
  </div>
  <div>
    Date<br>
    <input type="text" data-omise="expiration_month" value="12" size="4"> /
    <input type="text" data-omise="expiration_year" value="2024" size="8">
  </div>
  <div>
    Security Code<br>
    <input type="text" data-omise="security_code" value="123" size="8">
  </div>

  <input type="submit" id="create_token">
</form>
<script>
$("#checkout").submit(function () {

  var form = $(this);

  // Disable the submit button to avoid repeated click.
  form.find("input[type=submit]").prop("disabled", true);

  // Serialize the form fields into a valid card object.
  var card = {
    "name": form.find("[data-omise=holder_name]").val(),
    "number": form.find("[data-omise=number]").val(),
    "expiration_month": form.find("[data-omise=expiration_month]").val(),
    "expiration_year": form.find("[data-omise=expiration_year]").val(),
    "security_code": form.find("[data-omise=security_code]").val()
  };

  // Send a request to create a token then trigger the callback function once
  // a response is received from Omise.
  //
  // Note that the response could be an error and this needs to be handled within
  // the callback.
  Omise.createToken("card", card, function (statusCode, response) {
    if (response.object == "error") {
      // Display an error message.
      $("#token_errors").html(response.message);

      // Re-enable the submit button.
      form.find("input[type=submit]").prop("disabled", false);
    } else {
      // Then fill the omise_token.
      form.find("[name=omise_token]").val(response.id);

      // Remove card number from form before submiting to server.
      form.find("[data-omise=number]").val("");
      form.find("[data-omise=security_code]").val("");

      // submit token to server.
      form.get(0).submit();
    };
  });

  // Prevent the form from being submitted;
  return false;

});

</script>