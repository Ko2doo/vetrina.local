<?php

class User extends Controller{

	function defaultAction() {
		
	}

	// вход
	function loginAction() {
		$errors 	= [];
		$email 		= '';
		$pass			= '';
		$remember = true;
		
		// если форма отправлена
		if ( isset($_POST['submit']) ) {
			// получаем данные с формы
			$email 		= $_POST['email'];
			$pass			= $_POST['pass'];
			$remember = $_POST['remember'];

			$db		= App::db();
			$user = null;
			$hash = PASSWORD_DEFAULT;

			// проверка email
			if ( $email == '' ) {
				$errors['email'][] = 'Введите email';
			} else{
				$sql = 'SELECT * FROM users WHERE email = '.$db->quote($email);
				$result	= $db->query($sql)->fetch();
					if ( $result === false ) {
						$errors['email'][] = 'Нет такого пользователя';
					} else {
						$user = $result;
					}
			}

			// проверка пароля
			if ( $pass == '' ) {
				$errors['pass'][] = 'Введите пароль';
			} else {
				if ( $user ) {
					if ( password_verify($pass, $hash) ){
						$errors['pass'][] = 'Неверный пароль';
					}
				}
			}

			// авторизуем пол-я
			if ( empty($errors) ) {
				session_start();
				$_SESSION['user'] = $user;
				$this->redirect('/user/advert');
			}


		} //---endif

		$this->view('login',
			['email' => $email, 'pass' => $pass, 'remember' => $remember, 'errors' => $errors]);
	}

	// выход
	function logoutAction() {
		unset($_SESSION['user']);
		$this->redirect('/');
	}
	
	function registerAction() {
		// проверяем корректность ввода в форму:
		$errors 	= [];
		$email 		= '';
		$pass			= '';

		// если форма пуста:
		if ( isset($_POST['submit']) ) {
			// получаем данные с формы
			$email 			= trim(strip_tags($_POST['email']));
			$name				= trim(strip_tags($_POST['name']));
			$pass				= trim(strip_tags($_POST['pass']));
			$rep_pass		= trim(strip_tags($_POST['rep_pass']));

			$db		= App::db();
			$hash = PASSWORD_DEFAULT;

			// проверка email:
			if ( empty($email) ) {
				//-->
				$errors['email'][] = 'Введите email';
				//<--
			} else {
				$valid = filter_var($email, FILTER_VALIDATE_EMAIL);

					if ( !$valid ) {
						$errors['email'][] = 'Неправильный email';
					} else {
							// сравниваем введенный email с данными в БД
							$sql 	= 'SELECT COUNT(*) FROM users WHERE email = ?';
							$stmt = $db->prepare($sql);
							$stmt->bindValue(1, $email);

							// Проверяем успешность запроса
							if ( $stmt->execute() ) {
								// существует ли пол-ель?
								$user_exist = $stmt->fetchColumn();

								// выводим ошибку если существует
								if ( $user_exist ) {
									$errors['email'][] = 'Пользователь с таким email уже существует!';
								}
							}

					}

			}

			// проверяем имя:
			if ( empty($name) ) {
				$errors['name'][] = 'Укажите своё имя';
			}

			// проверяем пароль:
			if ( empty($pass) ) {
				$errors['pass'][] = 'Введите пароль!';
			} else {
				$length = mb_strlen($pass);

					if ( $length < 8 ) {
						$errors['pass'][] = 'Пароль должен быть не меньше 8 символов!';
					} else {
						if ( $pass != $rep_pass ) {
							$errors['rep_pass'][] = 'Не совпадение паролей';
						} else {
							$pass = password_hash($pass, $hash);
						}
					}
			}

			if ( empty($errors) ) {
				session_start();
				$select = "INSERT INTO users (email, name, pass) VALUES('$email', '$name', '$pass')";
				$result = $db->query($select);
				$this->redirect('/user/login');
			}

		}//--endif

		$this->view('register',
			['email' => $email, 'name' => $name, 'pass' => $pass, 'errors' => $errors]);
	}

	// public function addAction() {
	// 	// функция публикации объявлений и добавления их в БД
	// 	$errors = [];
	// 	$name 	= '';
	// 	$descr	= '';
	// 	$db			= App::db();

	// 	// $post = $_POST;

	// 	$name	 	= iconv_strlen($post['name']);
	// 	$descr 	= iconv_strlen($post['descr']);
	// 	$image 	= $post['image'];

	// 	if (isset($_POST['submit'])) {
			
	// 		// проверяем указано ли название:
	// 		if (empty($name)) {
	// 			$errors['name'][] = 'Укажите название';
	// 		} else {
	// 			if ( $name < 4 or $name > 100 ) {
	// 				$errors['name'][] = 'Название должно содержать от 4 до 100 символов';
	// 			}
	// 		}

	// 		// проверяем указано ли описание:
	// 		if (empty($descr)) {
	// 			$errors['descr'][] = 'Укажите описание';
	// 		} else {
	// 			if ( $descr < 10 or $descr > 5000 ) {
	// 				$errors['descr'][] = 'Текст должен содержать от 10 до 5000 символов';
	// 			}
	// 		}

 //      // проверка на файл
 //      if (empty($_FILES['img'])) {
 //        $errors['image'][] = 'Изображение не выбрано';
 //          return true; // пропускаем без картинки
 //      }

	// 		if ( empty($errors) ) {
	// 			session_start();
	// 			$select = "INSERT INTO posts (name, descr, image) VALUES('$name', '$descr', '$image')";
	// 			$result = $db->query($select);
	// 			$this->message('Добавлено');
	// 		}


	// 	$this->view('cabinet',
	// 		['name' => $name, 'descr' => $descr, 'image' => $image, 'errors' => $errors]);

	// 	} //--end-if
	// }

	static function isLoggedIn() {
		return isset($_SESSION['user']);
	}

	function advertAction() {

		// проверяем авторизацию юзера
		if ( !User::isLoggedIn() ) {
			$this->redirect('/user/login');
		}

		$this->view('advert');
	}
}