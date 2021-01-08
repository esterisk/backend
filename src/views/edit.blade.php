@extends($layout)

@section('content')

	<section class="edit-form">

	@include('esterisk.form.form', [ 'form' => $form ])
	<!-- @ form($form) -->

	</section>

@endsection
