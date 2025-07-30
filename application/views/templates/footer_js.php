<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/js/bootstrap.min.js" integrity="sha512-EKWWs1ZcA2ZY9lbLISPz8aGR2+L7JVYqBAYTq5AXgBkSjRSuQEGqWx8R1zAX16KdXPaCjOCaKE8MCpU0wcHlHA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    var base_url = "<?= base_url(); ?>";
	function loading_page(title,pesan) {
		if(title == ''){
			title = "Please Wait...";
		}
		Swal.fire({
			title: "<font style='color:white'>"+title+"</font>",
			html:"<font style='color:white'>"+pesan+"</font>",
			imageUrl: "<?= base_url("assets/image/loading.svg"); ?>",
			background:'rgba(0,0,0,0)',
			showConfirmButton: false,
			allowOutsideClick: false
		});
	}
</script>
