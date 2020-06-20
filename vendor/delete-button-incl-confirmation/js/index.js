// Design / Dribbble by:
// Adam Whitcroft
// URL: https://dribbble.com/shots/969445-The-Double-Delete

$("button").click(function(){
	if($(this).hasClass("confirm")){
		$(this).addClass("done");
		$("span.delete-btn-order").text("Deleted");
	} else {
		$(this).addClass("confirm");
		$("span.delete-btn-order").text("Are you sure?");
	}
});

// Reset
$("button.centerMe").on('mouseout', function(){
	if($(this).hasClass("confirm") || $(this).hasClass("done")){
		setTimeout(function(){
			$("button.centerMe").removeClass("confirm").removeClass("done");
			$("span.delete-btn-order").text("Delete");
		}, 3000);
	}
});