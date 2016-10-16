$(".poll-btn").click(function(){
	alert();
	var v = $(this).children().first().text();
	var sib = $(this).siblings();

	if (!$(this).hasClass("poll-btn-clicked")) {
		$(this).children().first().html(++v);
	}else{
		$(this).children().first().html(--v);
	}

	if (sib.hasClass("poll-btn-clicked")) {
		sib.removeClass("poll-btn-clicked");
		sib.children().first().html(--v);
	};

	$(this).toggleClass("poll-btn-clicked");
})
