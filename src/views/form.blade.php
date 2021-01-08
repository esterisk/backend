@extends($resource->viewLayout)

@section('content')

<section class="edit-form">

@include('esterisk.form.form', [ 'form' => $form ])
<!-- @ form($form) -->

</section>

</div>
@endsection
