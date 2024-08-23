<?php
require_once 'models/User.php';
require_once 'models/Category.php';
require_once 'models/Book.php';

class DashboardController{
    private $conn;
    private $userModel;
    private $categoryModel;
    private $bookModel;
    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->userModel = new User($conn);
        $this->categoryModel = new Category($conn);
        $this->bookModel = new Book($conn);
    }

    public function is_admin($userId){
        $user = $this->userModel->getUserById($userId);
        return $user['role'] === 'admin';
    }

    public function addCategory($category_name){
        if(!$category_name){
            return 'harap masukkan nama category';
        }

        if($this->categoryModel->getCategoryByName($category_name)){
            return 'category sudah pernah dibuat';
        }
        
        $result = $this->categoryModel->createCategory($category_name);
        if(!$result){
            return 'terjadi kesalahan, silakan coba lagi';
        }

        return 'success';
    }

    public function addBook($title, $category_id, $description, $file, $cover_image, $user_id){
        if(!$title || !$category_id || !$description || !$file || !$cover_image || !$user_id){
            return 'semua field wajib diisi';
        }
        
        $isSaveBook = $this->bookModel->saveBook($file, $cover_image);
        if($isSaveBook !== 'success'){
            return $isSaveBook;
        }
        
        $file_path = 'uploads/files/'.$file['name'];
        $cover_path = 'uploads/covers/'.$cover_image['name'];
        
        if(!$this->bookModel->createBook($title, $category_id, $description, $file_path, $cover_path, $user_id)){
            return 'terjadi kesalahan, silahkan coba lagi nanti';
        }
        return 'success';
    }   

    public function usersList(){
        $users = $this->userModel->getUsers();
        return $users;
    }

    public function categoriesList(){
        $categories = $this->categoryModel->getCategories();
        return $categories;
    }

    public function booksList(){
        $books = $this->bookModel->getBooks();
        return $books;
    }
}