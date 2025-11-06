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
