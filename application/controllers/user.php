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

	// для страницы добавления категорий
	function cat_settingsAction(){
		// проверяем авторизацию юзера
		if ( !User::isLoggedIn() ) {
			$this->redirect('/user/login');
		}

		// функция публикации объявлений и добавления их в БД
		$errors = [];
		$cat_name 		= '';
		$subcat_name	= '';
		$db	= App::db();


		if (isset($_POST['submit'])) {
			$cat_name	 		= iconv_strlen($_POST['cat_name']);
			$subcat_name 	= iconv_strlen($_POST['subcat_name']);

			// проверяем введено ли название
			if ( empty($cat_name) ) {
				$errors['cat_name'][] = 'Введите название категории!';
			} else{
				if ( $cat_name < 4 or $cat_name > 35 ) {
					$errors['cat_name'][] = 'Название должно содержать от 4 до 35 символов';
				} else {
						// сравниваем с данными в БД
						$sql 	= 'SELECT COUNT(*) FROM cat WHERE name = ?';
						$stmt = $db->prepare($sql);
						$stmt->bindValue(1, $cat_name);

						// Проверяем успешность запроса
						if ( $stmt->execute() ) {
							// существует ли запись?
							$name_exist = $stmt->fetchColumn();

							// выводим ошибку если существует
							if ( $name_exist ) {
								$errors['cat_name'][] = 'Такая запись уже существует!';
							}
						}
					}
			}

			// проверяем второе поле
			if ( empty($subcat_name) ) {
				$errors['subcat_name'][] = 'Введите название подкатегории';
			} else {
					if ( $subcat_name < 4 or $subcat_name > 35 ) {
						$errors['subcat_name'][] = 'Название должно содержать от 4 до 35 символов';
					}
			}

		} //--end if!

		$this->view('cat_settings',
			['cat_name' => $cat_name, 'subcat_name' => $subcat_name, 'errors' => $errors]);
	}
}