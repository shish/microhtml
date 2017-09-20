MicroHTML
=========

Moving from Hack to vanilla PHP, I miss XHP T\_\_T

This isn't XHP, but it does provide a minimum-bullshit method of generating HTML

```
$page = HTML(
	SECTION(["id"=>"news"],
		H1("My title"),
		P("Here's some content")
	)
);

$page->appendChild(
	SECTION(["id"=>"comments"],
		DIV("Oh noes: <script>alert('a haxxor is attacking us');</script>")
	)
);

print($page);
```

```
<html>
	<section id='news'>
		<h1>My title</h1>
		<p>Here&#039;s some content</p>
	</section>
	<section id='comments'>
		<div>Oh noes: &lt;script&gt;alert(&#039;a haxxor is attacking us&#039;);&lt;/script&gt;</div>
	</section>
</html>
```

