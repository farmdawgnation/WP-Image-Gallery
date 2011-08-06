<div class="wrap">
	<h2>
		Manage Image Galleries
		<a href="?page=wpig&action=add" class="button add-new-h2">Add New</a>
	</h2>
	
	<table class="widefat fixed">
		<thead>
		<tr>
			<th scope="col" class="manage-column">Cover</th>
			<th scope="col" class="manage-column">Title</th>
			<th scope="col" class="manage-column">Slug</th>
			<th scope="col" class="manage-column">Actions</th>
		</tr>
		</thead>
		<? foreach($galleries as $gallery) { ?>
		<tr>
			<td>PREVIEW</td>
			<td><?php echo $gallery->title ?></td>
			<td><?php echo $gallery->slug ?></td>
			<td>
				<a href="?page=wpig&action=view&id=<?php echo $gallery->id ?>">Photos</a> |
				<a href="?page=wpig&action=edit&id=<?php echo $gallery->id ?>">Edit</a> |
				<a href="?page=wpig&action=delete&id=<?php echo $gallery->id ?>" class="submitdelete" onclick="return confirm('Are you sure you want to delete this gallery?')">Delete</a>
			</td>
		</tr>
		<? } ?>
	</table>
</div>