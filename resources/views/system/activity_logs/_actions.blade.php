<div class="btn-group btn-group-sm" role="group">
  <a href="{{ route('activity-logs.show', $log->id) }}" class="btn btn-outline-primary">
    <i class="fa fa-eye"></i>
  </a>
  <button type="button" class="btn btn-outline-danger btn-del" data-id="{{ $log->id }}">
    <i class="fa fa-trash"></i>
  </button>
</div>

<script type="module">
$('.btn-del').off('click').on('click',function(){
  const id = $(this).data('id');
  Swal.fire({
    title: 'Delete Log?',
    text: 'This will soft-delete the log record.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it'
  }).then(res=>{
    if(res.isConfirmed){
      $.ajax({
        url: '{{ url("activity-logs") }}/'+id,
        type: 'DELETE',
        data: {_token:'{{ csrf_token() }}'},
        success: ()=> location.reload()
      });
    }
  });
});
</script>
