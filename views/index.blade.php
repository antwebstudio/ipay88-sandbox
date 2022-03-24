<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" integrity="sha512-P5MgMn1jBN01asBgU0z60Qk4QxiXo86+wlFahKrsQf37c9cro517WzVSPPV1tDKzhku2iJ2FVgL67wG03SGnNA==" crossorigin="anonymous" />

{{-- @dd($success) --}}
{{-- @dd($errors) --}}

<div class="container">

<div class="row">
    <div class="col-12 col-md-6">
        <div class="card card-body my-3">
            <h2>Request</h2>
            <pre>@php print_r($request->all()) @endphp</pre>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card card-body my-3">
            <h2>Config</h2>
            <p><b>Merchant Code: </b>{{ $sandbox->merchantCode }}</p>
            <p><b>Merchant Key: </b>{{ $sandbox->merchantKey }}</p>
        </div>
        <div class="card card-body my-3">
			<h2>Signature</h2>
            <p><b>Expected Signature: </b>{{ $sandbox->getExpectedSignature() }}</p>
            <p><b>Signature String: </b>{{ $sandbox->getSignatureString() }}</p>
			
		</div>
    </div>
</div>

<div class="row">

    <div class="col-12"><h2>Response</h2></div>

    @if ($errors->any())
        <div class="col-12">
            <div class="alert alert-danger">
                <ul>
                    <h2>Errors</h2>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <div class="col-12 col-md-6">
            <div class="card card-body my-3">
                <pre>@php print_r($successResponse->params) @endphp</pre>
            </div>

            <form action="{{ $successResponse->url }}" method="{{ $successResponse->method }}">
                @foreach ($successResponse->params as $key => $param)
                    <input type="hidden" name="{{ $key }}" value="{{ $param}}"/>
                @endforeach

                <button class="btn btn-success" type="submit">Return Success</button>
            </form>

            <form action="{{ $backendSuccessResponse->url }}" method="{{ $backendSuccessResponse->method }}">
                @foreach ($backendSuccessResponse->params as $key => $param)
                    <input type="hidden" name="{{ $key }}" value="{{ $param}}"/>
                @endforeach

                <p>You should see "RECEIVEOK" in a blank white page after click this: </p>

                <button class="btn btn-success" type="submit">Return Backend Success</button>
            </form>

        </div>
    @endif

    <div class="col-12 col-md-6">
        <div class="card card-body my-3">
            <pre>@php print_r($errorResponse->params) @endphp</pre>
        </div>

        <form action="{{ $errorResponse->url }}" method="{{ $errorResponse->method }}">
            @foreach ($errorResponse->params as $key => $param)
                <input type="hidden" name="{{ $key }}" value="{{ $param}}"/>
            @endforeach

            <button class="btn btn-danger" type="submit">Return Error</button>
        </form>

    </div>

</div>