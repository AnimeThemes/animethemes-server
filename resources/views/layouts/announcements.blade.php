@foreach ($announcements as $announcement)
    <p class="alert alert-info">{!! $announcement->content !!}<p>
@endforeach