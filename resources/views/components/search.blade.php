@props(['title'])
<section id="knowledge-base-search">
    <div class="row">
        <div class="col-12">
            <div class="card knowledge-base-bg text-center"
                style="background-image: url({{ asset('assets/images/banner/banner.png') }})">
                <div class="card-body">
                    <h2 class="text-primary mb-2">{{ $title }}</h2>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</section>
