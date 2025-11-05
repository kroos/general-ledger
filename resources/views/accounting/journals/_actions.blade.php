<div class="btn-group btn-group-sm" role="group">
  @if($j->status === 'draft')
    <form method="POST" action="{{ route('journals.post', $j) }}" class="d-inline">
      @csrf
      <button class="btn btn-success btn-sm">
        <i class="fa fa-check"></i> Post
      </button>
    </form>
  @elseif($j->status === 'posted')
    <form method="POST" action="{{ route('journals.unpost', $j) }}" class="d-inline">
      @csrf
      <button class="btn btn-warning btn-sm">
        <i class="fa fa-undo"></i> Unpost
      </button>
    </form>
  @endif

  <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $j->id }}">
    <i class="fa fa-trash"></i>
  </button>
</div>

<script type="module">
$('.btn-delete').off('click').on('click', function(){
  const id = $(this).data('id');
  swal.fire({
    title: 'Delete Journal?',
    text: 'This will soft-delete the record.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it'
  }).then(res=>{
    if(res.isConfirmed){
      $.ajax({
        url: '{{ url("journals.destroy") }}/'+id,
        type: 'DELETE',
        data: {_token:'{{ csrf_token() }}'},
        success: ()=> location.reload()
      });
    }
  });
});
</script>
