@php
use App\Models\Accounting\Journal;

$draftCount = Journal::where('status', 'draft')->count();
$drafts = Journal::where('status', 'draft')
		->latest()
		->take(5)
		->get(['id','date','description']);
@endphp

<li class="nav-item dropdown">
	<a class="nav-link position-relative" href="#" id="draftDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
		<i class="fa fa-bell"></i>
		@if($draftCount > 0)
			<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
				{{ $draftCount }}
			</span>
		@endif
	</a>
	<ul class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="draftDropdown" style="min-width:300px;">
		<li class="dropdown-header bg-light fw-bold px-3 py-2">
			Draft Journals
		</li>
		@if($drafts->isEmpty())
			<li><span class="dropdown-item text-muted small">No draft journals</span></li>
		@else
			@foreach($drafts as $d)
				<li class="dropdown-item d-flex justify-content-between align-items-center small">
					<div>
						<strong>#{{ $d->id }}</strong> — {{ $d->description ?? 'No desc' }}<br>
						<span class="text-muted">{{ $d->date->format('d M Y') }}</span>
					</div>
					<div class="btn-group btn-group-sm">
						<a href="{{ route('journals.index') }}?open={{ $d->id }}" class="btn btn-outline-primary btn-sm">
							<i class="fa fa-eye"></i>
						</a>
						<button type="button" class="btn btn-outline-danger btn-sm btn-del-draft" data-id="{{ $d->id }}">
							<i class="fa fa-trash"></i>
						</button>
					</div>
				</li>
			@endforeach
		@endif
		<li><hr class="dropdown-divider"></li>
		<li><a href="{{ route('journals.index') }}" class="dropdown-item text-center small text-primary">View all journals</a></li>
	</ul>
</li>

<script type="module">
$('.btn-del-draft').off('click').on('click',function(){
	const id = $(this).data('id');
	swal.fire({
		title: 'Delete Draft?',
		text: 'This will permanently delete this draft journal.',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Delete'
	}).then(res=>{
		if(res.isConfirmed){
			$.ajax({
				url: '{{ url("journals.destroy") }}/'+id,
				type: 'DELETE',
				data: {_token: '{{ csrf_token() }}'},
				success: ()=> location.reload()
			});
		}
	});
});

function refreshDraftCount(){
	$.get('{{ route("journals.draft-count") }}', data=>{
		const badge = $('.fa-bell').next('span.badge');
		if(data.count > 0){
			if(badge.length) badge.text(data.count);
			else $('.fa-bell').after(`<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">${data.count}</span>`);
		} else {
			badge.remove();
		}
	});
}

setInterval(refreshDraftCount, 60000); // refresh every 60s

</script>
