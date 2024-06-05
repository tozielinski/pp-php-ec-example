<?php
include_once 'api/config/Config.php';

$payload = file_get_contents("payload.json");

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- <meta http-equiv="Content-Security-Policy" content="form-action https://www.sandbox.paypal.com/checkoutnow" /> -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
		<title>PayPal EC Standard Integration</title>
	</head>
	<body>
	<h1>Direct API Integration</h1>
	<div id="payload-container"></div>
	<form id="createOrderForm" style='display:inline;'>
		<input id="createOrderInput" type="submit" value="create order"/>
	</form>
	<!-- <form id="approvalOrderForm" action="<?= $url ?>"> -->
	<form id="approvalOrderForm" action="https://focus.de" method="post" target="_blank" style='display:inline;'>
		<input id="approvalOrderInput" type="submit" value="approve order" disabled/>
	</form>
	<form id="getOrderForm" style='display:inline;'>
		<input id="getOrderInput" type="submit" value="get order details (optional)" disabled/>
	</form>
	<form id="captureOrderForm" style='display:inline;'>
		<input id="captureOrderInput" type="submit" value="capture payment for order" disabled/>
	</form>
	<hr/>
	<div id="response-container"></div>
	<script>
		$(document).ready(function () {
			function writeResponse (containerTitle, summaryTitle, content) {
				const container = document.getElementById(containerTitle);
				const details = document.createElement("details");
				const summary = document.createElement("summary");
				summary.innerHTML = summaryTitle;
				const pre = document.createElement("pre");
				pre.innerHTML = '<p>'+JSON.stringify(content, null, 2)+'</p>';
				const hr = document.createElement("hr");
				container.appendChild(details);
				details.appendChild(summary);
				details.appendChild(pre);
				container.appendChild(hr);
			}

			let orderId = "";

			$('#createOrderForm').submit(function (event) {
				event.preventDefault();
				// var form = document.getElementById('createOrderForm'); 
				var formData = new FormData();
				formData.append("payload", JSON.stringify(<?= $payload ?>));
				writeResponse("payload-container", "Payload", <?= $payload ?>);

				$.ajax({
					// url: <?= $rootPath.URL['services']['orderCreate'] ?>,
					url: 'api/createOrder.php',
					method: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function(response) {
						const errorDetail = response.response?.details?.[0];
						orderId = response.response?.id;
						if (orderId) {
							$('#approvalOrderForm').attr('action', response.response.links[1].href);
							$('#createOrderInput').attr('disabled', true);
							$('#approvalOrderInput').attr('disabled', false);
							$('#getOrderInput').attr('disabled', false);
							writeResponse("response-container", "Create Order Response", response.response);
							console.log(response);
						} else {
							writeResponse("response-container", "ERROR", "Sorry, your create order could not be processed. <br>"+errorDetail?.description+"<br/>"+response.response?.debug_id);
							console.error(response);
						}
					},
					error: function(xhr, textStatus, error){
						alert('Your form was not sent successfully.');
						writeResponse("response-container", "ERROR", error);
						console.error(error); 
					}
				});
			});
			$('#approvalOrderForm').submit(function (event) {
				$('#approvalOrderInput').attr('disabled', true);
				$('#captureOrderInput').attr('disabled', false);
			});
			$('#getOrderForm').submit(function (event) {
				event.preventDefault();

				$.ajax({
					url: 'api/getOrderDetails.php?id='+orderId,
					method: 'GET',
					success: function(response) {
						const errorDetail = response.response?.details?.[0];
						const responseId = response.response?.id;
						if (responseId === orderId) {
							writeResponse("response-container", "Get Order Detail Response", response.response);
							console.log(response);
						} else {
							writeResponse("response-container", "ERROR", "Sorry, your get order-details could not be processed. <br>"+errorDetail?.description+"<br/>"+response.response?.debug_id);
							console.error(response);
						}
						
					},
					error: function(xhr, textStatus, error){
						alert('Your form was not sent successfully.');
						writeResponse("response-container", "ERROR", error);
						console.error(error); 
					}
				});
			});
			$('#captureOrderForm').submit(function (event) {
				event.preventDefault();

				$.ajax({
					url: 'api/capturePaymentForOrder.php?id='+orderId,
					method: 'GET',
					success: function(response) {
						const errorDetail = response.response?.details?.[0];
						const transaction = response.response?.purchase_units?.[0]?.payments?.captures?.[0] ||
							response.response?.purchase_units?.[0]?.payments?.authorizations?.[0];
						if (transaction?.status === "COMPLETED" || transaction?.status === "CREATED") {
							writeResponse("response-container", "Capture Payment For Order Response", response.response);
							console.log(response);
						} else {
							writeResponse("response-container", "ERROR", "Sorry, your capture payment for order could not be processed. <br>"+errorDetail?.description+"<br/>"+response.response?.debug_id);
							console.error(response);
						}
					},
					error: function(xhr, textStatus, error){
						alert('Your form was not sent successfully.');
						writeResponse("response-container", "ERROR", error);
						console.error(error); 
					}
				});
			});
		});
	</script>
  </body>
</html>