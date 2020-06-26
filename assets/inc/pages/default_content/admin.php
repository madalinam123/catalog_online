<div class="card">
<div class="card-header"><h6>Statistica DataBase</h6></div>
<div class="card-body">

<table class="table table-bordered" style="font-size:14px;">
<?php
$users = mysqli_num_rows(mysqli_query($con,"SELECT id FROM users"));
$teachers = mysqli_num_rows(mysqli_query($con,"SELECT id FROM users WHERE user_rank ='teacher'"));
$students = mysqli_num_rows(mysqli_query($con,"SELECT id FROM users WHERE user_rank ='student'"));
$admins = mysqli_num_rows(mysqli_query($con,"SELECT id FROM users WHERE user_rank ='admin'"));
$materii = mysqli_num_rows(mysqli_query($con,"SELECT cat_id FROM materii"));
$grades = mysqli_num_rows(mysqli_query($con,"SELECT id FROM grades"));
?>
<tr>
<td>Useri</td>
<td><?php echo $users;?></td>
</tr>
<tr>
<td>Profesori</td>
<td><?php echo $teachers;?></td>
</tr>
<tr>
<td>Studenti</td>
<td><?php echo $students;?></td>
</tr>
<tr>
<td>Admini</td>
<td><?php echo $admins;?></td>
</tr>
<tr>
<td>Materii</td>
<td><?php echo $materii;?></td>
</tr>
<tr>
<td>Note</td>
<td><?php echo $grades;?></td>
</tr>
</table>
</div>
</div>