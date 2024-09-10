<?php
	//กำหนดค่า Access-Control-Allow-Origin ให้ เครื่อง อื่น ๆ สามารถเรียกใช้งานหน้านี้ได้
	header("Access-Control-Allow-Origin: *");	
	header("Content-Type: application/json; charset=UTF-8");	
	header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");	
	header("Access-Control-Max-Age: 3600");	
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	
	//ตั้งค่าการเชื่อมต่อฐานข้อมูล
	$link = mysqli_connect('192.168.1.2', 'root', 'Cpe123456', 'lab7');
	mysqli_set_charset($link, 'utf8');	
	$requestMethod = $_SERVER["REQUEST_METHOD"];	
	//ตรวจสอบหากใช้ Method GET
	if($requestMethod == 'GET'){					
		$course_code = $_GET['course_code'];	
		//ตรวจสอบการส่งค่า code
		if(isset($_GET['student_code']) && !empty($_GET['student_code'])){			
			$student_code = $_GET['student_code'];		
			//คำสั่ง SQL กรณี มีการส่งค่า student_code มาให้แสดงเฉพาะข้อมูลของ student_code นั้น
			$sql = "SELECT * FROM exam_results WHERE student_code = '$student_code' AND course_code = '$course_code'";		
		}else{
			//คำสั่ง SQL แสดงข้อมูลทั้งหมด
			$sql = "SELECT * FROM exam_results where course_code = '$course_code'";
		}		
		$result = mysqli_query($link, $sql);		
		//สร้างตัวแปร array สำหรับเก็บข้อมูลที่ได้
		$arr = array();
				while ($row = mysqli_fetch_assoc($result)) {
			 
			 $arr[] = $row;
		}		
		echo json_encode($arr);
	}
	//อ่านข้อมูลที่ส่งมาแล้วเก็บไว้ที่ตัวแปร data
	$data = file_get_contents("php://input");
	//แปลงข้อมูลที่อ่านได้ เป็น array แล้วเก็บไว้ที่ตัวแปร result
	$result = json_decode($data,true);
	//ตรวจสอบการเรียกใช้งานว่าเป็น Method POST หรือไม่
	if($requestMethod == 'POST'){		
		if(!empty($result)){			
			$student_code = $result['student_code'];
			$course_code = $result['course_code'];
			$point = $result['point'];
			
			//คำสั่ง SQL สำหรับเพิ่มข้อมูลใน Database
			$sql = "INSERT INTO exam_results (student_code,course_code,point) VALUES ('$student_code','$course_code','$point')";
			
			$result = mysqli_query($link, $sql);
			
			if ($result) {
			   echo json_encode(['status' => 'ok','message' => 'Insert Data Complete']);
			} else {
			   echo json_encode(['status' => 'error','message' => 'Error']);	
			}
		}
			
	}
	//ตรวจสอบการเรียกใช้งานว่าเป็น Method PUT หรือไม่
	if($requestMethod == 'PUT'){
		$course_code = $result['course_code'];
		$student_code = $result['student_code'];
		$point = $result['point'];
		
		//คำสั่ง SQL สำหรับแก้ไขข้อมูลใน Database โดยจะแก้ไขเฉพาะข้อมูลตามค่า student_code ที่ส่งมา
		$sql = "UPDATE exam_results SET point = '$point' WHERE student_code = '$student_code' AND course_code='$course_code'";

		$result = mysqli_query($link, $sql);
		
		if ($result) {
			echo json_encode(['status' => 'ok','message' => 'Update Data Complete']);
		} else {
			echo json_encode(['status' => 'error','message' => 'Error']);	
		}
	
	}
	//ตรวจสอบการเรียกใช้งานว่าเป็น Method DELETE หรือไม่
	if($requestMethod == 'DELETE'){
		$course_code = $_GET['course_code'];
		//ตรวจสอบว่ามีการส่งค่า student_code มาหรือไม่
		if(isset($_GET['student_code']) && !empty($_GET['student_code'])){			
			$student_code = $_GET['student_code'];			
			//คำสั่ง SQL สำหรับลบข้อมูลใน Database ตามค่า student_code ที่ส่งมา
			$sql = "DELETE FROM exam_results WHERE student_code = '$student_code' AND course_code='$course_code'";
			$result = mysqli_query($link, $sql);			
			if ($result) {
			   echo json_encode(['status' => 'ok','message' => 'Delete Data ($student_code) Complete']);
			} else {
			   echo json_encode(['status' => 'error','message' => 'Error']);	
			}
		}	
	}
?>
