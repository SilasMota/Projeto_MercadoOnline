
<footer style="color:white; background-color: #404040;">
	<div>
		<span class="fa fa-shopping-cart"></span> <span>Mercado Online</span><br>
		<span class="fas fa-envelope"></span> <span>mercadoonline@email.com</span><br>
		<span class="fas fa-map-marker-alt"></span> <span>Nome da Rua , 123 , Cidade</span>
	</div>
</footer> 
<script type="text/javascript">

	//controla expanção da barra
	window.onscroll = function() {scrollFunction()};

	function scrollFunction() {
	  if (document.body.scrollTop > 0 || document.documentElement.scrollTop > 0) {
	    document.getElementById("navbar").style.padding = "0 10px";
	    document.getElementById("logo").style.fontSize = "1.5em";
	  } else {
	    document.getElementById("navbar").style.padding = "8px 10px";
	    document.getElementById("logo").style.fontSize = "1.7em";
	  }
	}
	

	//controla rolagem do menu de categorias
	$(".dropdown").on("show.bs.dropdown", 
		function desfixa(){
			if (window.matchMedia("(max-width: 992px)").matches)
				document.getElementById("navbar").style.position = "static";
		});

	$(".dropdown").on("hidden.bs.dropdown", 
		function desfixa(){
				document.getElementById("navbar").style.position = "sticky";
				document.getElementById("navbar").style.top = "0";
				document.getElementById("navbar").style.widith = "100%";
		});
					
</script>