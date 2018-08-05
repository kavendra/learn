
<ul class="list-group list-group-flush">
	<li class="list-group-item"><a href="{{ route('users.edit', $user->id)}}"> Profile</a></li>  
	<li class="list-group-item"><a href="{{ route('user.projects.index', $user->id)}}"> Projects</a></li>                                                      
</ul>