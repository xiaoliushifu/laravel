<div>
	<ul>
		<li><a href="/test/fileform">文件</a></li>
		<li><a href="/test">暂时无</a></li>
	</ul>
</div>
<div>
<img src="/images/Tulips.jpg" />
	<form action="/test" method="post">
		<dl>
		<!--csrf跨站请求伪造的token-->
		{{ csrf_field() }}
			@foreach($fields as $key=>$val)
				<dd>{{ $val }}:<input type="text" name="{{ $val }}"></dd>
			@endforeach
			<dd><input type="submit" name="sub"></dd>
		</dl>
	</form>
</div>
