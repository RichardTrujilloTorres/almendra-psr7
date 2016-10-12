
[]		Route definition by annotations
[]		Server obj: handles related server variables, config, etc.
[]		Rewrite JWT
[]		https: how does that works?
[x]		validator: unique interface for different input types 


[x]		str find ignoring case for getHeader(), hasHeader(), etc.

// @todo use a header collection instead
// @todo more checks (header's validation)

// @todo remaining cases

[]		Stream validity checker

[x]		message
[]		headers  
[x]		Uri (finish it)
[x]		request
[x]		response
[]		stream


[]		fake put request
[]		helpers: target validation


[]		In Uri:
[]		// $this -> setScheme();
[]		...
[]		// @todo validate fragment

[]		In URI (helpers)
[]		getQueryFragment($uri)
[]		// @todo match against verificator
        // dd(self::validator());

[]		In Request:
// @todo enforce UriInterface, ServerInterface