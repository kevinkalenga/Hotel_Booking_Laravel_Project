<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminProfilController;
use App\Http\Controllers\Admin\AdminSlideController;
use App\Http\Controllers\Admin\AdminFeatureController;
use App\Http\Controllers\Admin\AdminPhotoController;
use App\Http\Controllers\Admin\AdminVideoController;
use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminSubscriberController;
use App\Http\Controllers\Admin\AdminAmenityController;
use App\Http\Controllers\Admin\AdminRoomController;

use App\Http\Controllers\Customer\CustomerHomeController;
use App\Http\Controllers\Customer\CustomerAuthController;
use App\Http\Controllers\Customer\CustomerProfileController;

use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\AboutController;
use App\Http\Controllers\Front\PhotoController;
use App\Http\Controllers\Front\VideoController;
use App\Http\Controllers\Front\FaqController;
use App\Http\Controllers\Front\TermsController;
use App\Http\Controllers\Front\PrivacyController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\SubscriberController;
use App\Http\Controllers\Front\RoomController;
use App\Http\Controllers\Front\BookingController;



/* ------------------------- Front--------------------- */

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/post/{id}', [BlogController::class, 'single_post'])->name('post');
Route::get('/photo-gallery', [PhotoController::class, 'index'])->name('photo_gallery');
Route::get('/video-gallery', [VideoController::class, 'index'])->name('video_gallery');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::get('/terms-and-conditions', [TermsController::class, 'index'])->name('terms');
Route::get('/privacy-policy', [PrivacyController::class, 'index'])->name('privacy');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact/send-email', [ContactController::class, 'send_email'])->name('contact_send_email');
Route::post('/subscriber/send-email', [SubscriberController::class, 'send_email'])->name('subscriber_send_email');
Route::get('/subscriber/verify/{email}/{token}', [SubscriberController::class, 'verify'])->name('subscriber_verify');
Route::get('/room', [RoomController::class, 'index'])->name('room');
Route::get('/room/{id}', [RoomController::class, 'single_room'])->name('room_detail');
Route::post('/booking/submit', [BookingController::class, 'cart_submit'])->name('cart_submit');
Route::get('/cart', [BookingController::class, 'cart_view'])->name('cart');
Route::get('/cart/delete/{id}', [BookingController::class, 'cart_delete'])->name('cart_delete');


/* ---------------------- Admin ---------------------- */



// Login
Route::get('/admin/login', [AdminLoginController::class, 'index'])->name('admin_login');
Route::post('/admin/login-submit', [AdminLoginController::class, 'login_submit'])->name('admin_login_submit');

// Logout
Route::get('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin_logout');

// Forget password
Route::get('/admin/forget-password', [AdminLoginController::class, 'forget_password'])->name('admin_forget_password');
Route::post('/admin/forget-password-submit', [AdminLoginController::class, 'forget_password_submit'])->name('admin_forget_password_submit');

// Reset pwd
Route::get('/admin/reset-password/{token}/{email}', [AdminLoginController::class, 'reset_password'])->name('admin_reset_password');
Route::post('/admin/reset-password/{token}/{email}', [AdminLoginController::class, 'reset_password_submit'])->name('admin_reset_password_submit');


// customer - Login, Logout and forget pwd
Route::get('/customer/logout', [CustomerAuthController::class, 'logout'])->name('customer_logout');
Route::get('/login', [CustomerAuthController::class, 'login'])->name('customer_login');
Route::post('/login-submit', [CustomerAuthController::class, 'login_submit'])->name('customer_login_submit');
Route::get('/signup', [CustomerAuthController::class, 'signup'])->name('customer_signup');
Route::post('/signup-submit', [CustomerAuthController::class, 'signup_submit'])->name('customer_signup_submit');
Route::get('/signup-verify/{email}/{token}', [CustomerAuthController::class, 'signup_verify'])->name('customer_signup_verify');
Route::get('/forget-password', [CustomerAuthController::class, 'forget_password'])->name('customer_forget_password');
Route::post('/forget-password-submit', [CustomerAuthController::class, 'forget_password_submit'])->name('customer_forget_password_submit');
Route::get('/reset-password/{token}/{email}', [CustomerAuthController::class, 'reset_password'])->name('customer_reset_password');
Route::post('/reset-password/{token}/{email}', [CustomerAuthController::class, 'reset_password_submit'])->name('customer_reset_password_submit');




// customer-middleware
Route::group(['middleware' =>['customer:customer']], function(){
     Route::get('/customer/home', [CustomerHomeController::class, 'index'])->name('customer_home');
     Route::get('/customer/edit-profile', [CustomerProfileController::class, 'index'])->name('customer_profile');
     Route::post('/customer/edit-profile-submit', [CustomerProfileController::class, 'profile_submit'])->name('customer_profile_submit');
});

// Admin - Middleware
Route::group(['middleware' => 'admin'], function(){

// Edit profile (GET + POST) ✅ middleware admin appliqué sur les deux

    Route::get('/admin/edit-profile', [AdminProfilController::class, 'index'])->name('admin_profile');
    Route::post('/admin/edit-profile-submit', [AdminProfilController::class, 'profile_submit'])->name('admin_profile_submit');

   // Dashboard
   Route::get('/admin/home', [AdminHomeController::class, 'index'])->name('admin_home');

   // Slide
   Route::get('/admin/slide/view', [AdminSlideController::class, 'index'])->name('admin_slide_view');
   Route::get('/admin/slide/add', [AdminSlideController::class, 'add'])->name('admin_slide_add');
   Route::post('/admin/slide/store', [AdminSlideController::class, 'store'])->name('admin_slide_store');
   Route::get('/admin/slide/edit/{id}', [AdminSlideController::class, 'edit'])->name('admin_slide_edit');
   Route::post('/admin/slide/edit/{id}', [AdminSlideController::class, 'update'])->name('admin_slide_update');
   Route::get('/admin/slide/delete/{id}', [AdminSlideController::class, 'delete'])->name('admin_slide_delete');
   // Photo
   Route::get('/admin/photo/view', [AdminPhotoController::class, 'index'])->name('admin_photo_view');
   Route::get('/admin/photo/add', [AdminPhotoController::class, 'add'])->name('admin_photo_add');
   Route::post('/admin/photo/store', [AdminPhotoController::class, 'store'])->name('admin_photo_store');
   Route::get('/admin/photo/edit/{id}', [AdminPhotoController::class, 'edit'])->name('admin_photo_edit');
   Route::post('/admin/photo/edit/{id}', [AdminPhotoController::class, 'update'])->name('admin_photo_update');
   Route::get('/admin/photo/delete/{id}', [AdminPhotoController::class, 'delete'])->name('admin_photo_delete');
   // Video
   Route::get('/admin/video/view', [AdminVideoController::class, 'index'])->name('admin_video_view');
   Route::get('/admin/video/add', [AdminVideoController::class, 'add'])->name('admin_video_add');
   Route::post('/admin/video/store', [AdminVideoController::class, 'store'])->name('admin_video_store');
   Route::get('/admin/video/edit/{id}', [AdminVideoController::class, 'edit'])->name('admin_video_edit');
   Route::post('/admin/video/edit/{id}', [AdminVideoController::class, 'update'])->name('admin_video_update');
   Route::get('/admin/video/delete/{id}', [AdminVideoController::class, 'delete'])->name('admin_video_delete');
   // Faq
   Route::get('/admin/faq/view', [AdminFaqController::class, 'index'])->name('admin_faq_view');
   Route::get('/admin/faq/add', [AdminFaqController::class, 'add'])->name('admin_faq_add');
   Route::post('/admin/faq/store', [AdminFaqController::class, 'store'])->name('admin_faq_store');
   Route::get('/admin/faq/edit/{id}', [AdminFaqController::class, 'edit'])->name('admin_faq_edit');
   Route::post('/admin/faq/edit/{id}', [AdminFaqController::class, 'update'])->name('admin_faq_update');
   Route::get('/admin/faq/delete/{id}', [AdminFaqController::class, 'delete'])->name('admin_faq_delete');
   // Feature
   Route::get('/admin/feature/view', [AdminFeatureController::class, 'index'])->name('admin_feature_view');
   Route::get('/admin/feature/add', [AdminFeatureController::class, 'add'])->name('admin_feature_add');
   Route::post('/admin/feature/store', [AdminFeatureController::class, 'store'])->name('admin_feature_store');
   Route::get('/admin/feature/edit/{id}', [AdminFeatureController::class, 'edit'])->name('admin_feature_edit');
   Route::post('/admin/feature/edit/{id}', [AdminFeatureController::class, 'update'])->name('admin_feature_update');
   Route::get('/admin/feature/delete/{id}', [AdminFeatureController::class, 'delete'])->name('admin_feature_delete');
   // Testimonial
   Route::get('/admin/testimonial/view', [AdminTestimonialController::class, 'index'])->name('admin_testimonial_view');
   Route::get('/admin/testimonial/add', [AdminTestimonialController::class, 'add'])->name('admin_testimonial_add');
   Route::post('/admin/testimonial/store', [AdminTestimonialController::class, 'store'])->name('admin_testimonial_store');
   Route::get('/admin/testimonial/edit/{id}', [AdminTestimonialController::class, 'edit'])->name('admin_testimonial_edit');
   Route::post('/admin/testimonial/edit/{id}', [AdminTestimonialController::class, 'update'])->name('admin_testimonial_update');
   Route::get('/admin/testimonial/delete/{id}', [AdminTestimonialController::class, 'delete'])->name('admin_testimonial_delete');
   //Post
   Route::get('/admin/post/view', [AdminPostController::class, 'index'])->name('admin_post_view');
   Route::get('/admin/post/add', [AdminPostController::class, 'add'])->name('admin_post_add');
   Route::post('/admin/post/store', [AdminPostController::class, 'store'])->name('admin_post_store');
   Route::get('/admin/post/edit/{id}', [AdminPostController::class, 'edit'])->name('admin_post_edit');
   Route::post('/admin/post/edit/{id}', [AdminPostController::class, 'update'])->name('admin_post_update');
   Route::get('/admin/post/delete/{id}', [AdminPostController::class, 'delete'])->name('admin_post_delete');
   
   // About Page
   Route::get('/admin/page/about', [AdminPageController::class, 'about'])->name('admin_page_about');
   Route::post('/admin/page/about/update', [AdminPageController::class, 'about_update'])->name('admin_page_about_update');
   // Terms
   Route::get('/admin/page/terms', [AdminPageController::class, 'terms'])->name('admin_page_terms');
   Route::post('/admin/page/terms/update', [AdminPageController::class, 'terms_update'])->name('admin_page_terms_update');
   // Privacy
   Route::get('/admin/page/privacy', [AdminPageController::class, 'privacy'])->name('admin_page_privacy');
   Route::post('/admin/page/privacy/update', [AdminPageController::class, 'privacy_update'])->name('admin_page_privacy_update');
   // Contact
   Route::get('/admin/page/contact', [AdminPageController::class, 'contact'])->name('admin_page_contact');
   Route::post('/admin/page/contact/update', [AdminPageController::class, 'contact_update'])->name('admin_page_contact_update');
   // Photo Gallery
   Route::get('/admin/page/photo-gallery', [AdminPageController::class, 'photo_gallery'])->name('admin_page_photo_gallery');
   Route::post('/admin/page/contact/photo-gallery/update', [AdminPageController::class, 'photo_gallery_update'])->name('admin_page_photo_gallery_update');
   // Video Gallery
   Route::get('/admin/page/video-gallery', [AdminPageController::class, 'video_gallery'])->name('admin_page_video_gallery');
   Route::post('/admin/page/video-gallery/update', [AdminPageController::class, 'video_gallery_update'])->name('admin_page_video_gallery_update');
   // Faq
   Route::get('/admin/page/faq', [AdminPageController::class, 'faq'])->name('admin_page_faq');
   Route::post('/admin/page/faq/update', [AdminPageController::class, 'faq_update'])->name('admin_page_faq_update');
   // Blog
   Route::get('/admin/page/blog', [AdminPageController::class, 'blog'])->name('admin_page_blog');
   Route::post('/admin/page/blog/update', [AdminPageController::class, 'blog_update'])->name('admin_page_blog_update');
   // Room
   Route::get('/admin/page/room', [AdminPageController::class, 'room'])->name('admin_page_room');
   Route::post('/admin/page/room/update', [AdminPageController::class, 'room_update'])->name('admin_page_room_update');
   // Cart
   Route::get('/admin/page/cart', [AdminPageController::class, 'cart'])->name('admin_page_cart');
   Route::post('/admin/page/cart/update', [AdminPageController::class, 'cart_update'])->name('admin_page_cart_update');
   // Checkout
   Route::get('/admin/page/checkout', [AdminPageController::class, 'checkout'])->name('admin_page_checkout');
   Route::post('/admin/page/checkout/update', [AdminPageController::class, 'checkout_update'])->name('admin_page_checkout_update');
   // Payment
   Route::get('/admin/page/payment', [AdminPageController::class, 'payment'])->name('admin_page_payment');
   Route::post('/admin/page/payment/update', [AdminPageController::class, 'payment_update'])->name('admin_page_payment_update');
   // SignUp
   Route::get('/admin/page/signup', [AdminPageController::class, 'signup'])->name('admin_page_signup');
   Route::post('/admin/page/signup/update', [AdminPageController::class, 'signup_update'])->name('admin_page_signup_update');
   // Forget Password
   Route::get('/admin/page/forget-password', [AdminPageController::class, 'forget_password'])->name('admin_page_forget_password');
   Route::post('/admin/page/forget-password/update', [AdminPageController::class, 'forget_password_update'])->name('admin_page_forget_password_update');
   // Reset Password
   Route::get('/admin/page/reset-password', [AdminPageController::class, 'reset_password'])->name('admin_page_reset_password');
   Route::post('/admin/page/reset-password/update', [AdminPageController::class, 'reset_password_update'])->name('admin_page_reset_password_update');
   //  SignIn  
   Route::get('/admin/page/signin', [AdminPageController::class, 'signin'])->name('admin_page_signin');
   Route::post('/admin/page/signin/update', [AdminPageController::class, 'signin_update'])->name('admin_page_signin_update');

   // subscriber
   Route::get('/admin/subscriber/show', [AdminSubscriberController::class, 'show'])->name('admin_subscriber_show');
   Route::get('/admin/subscriber/send-email', [AdminSubscriberController::class, 'send_email'])->name('admin_subscriber_send_email');
   Route::post('/admin/subscriber/send-email_submit', [AdminSubscriberController::class, 'send_email_submit'])->name('admin_subscriber_send_email_submit');

   // Amenity
   Route::get('/admin/amenity/view', [AdminAmenityController::class, 'index'])->name('admin_amenity_view');
   Route::get('/admin/amenity/add', [AdminAmenityController::class, 'add'])->name('admin_amenity_add');
   Route::post('/admin/amenity/store', [AdminAmenityController::class, 'store'])->name('admin_amenity_store');
   Route::get('/admin/amenity/edit/{id}', [AdminAmenityController::class, 'edit'])->name('admin_amenity_edit');
   Route::post('/admin/amenity/edit/{id}', [AdminAmenityController::class, 'update'])->name('admin_amenity_update');
   Route::get('/admin/amenity/delete/{id}', [AdminAmenityController::class, 'delete'])->name('admin_amenity_delete');
   // Room
   Route::get('/admin/room/view', [AdminRoomController::class, 'index'])->name('admin_room_view');
   Route::get('/admin/room/add', [AdminRoomController::class, 'add'])->name('admin_room_add');
   Route::post('/admin/room/store', [AdminRoomController::class, 'store'])->name('admin_room_store');
   Route::get('/admin/room/edit/{id}', [AdminRoomController::class, 'edit'])->name('admin_room_edit');
   Route::post('/admin/room/edit/{id}', [AdminRoomController::class, 'update'])->name('admin_room_update');
   Route::get('/admin/room/delete/{id}', [AdminRoomController::class, 'delete'])->name('admin_room_delete');

   // Room Photo
   Route::get('/admin/room/gallery/{id}', [AdminRoomController::class, 'gallery'])->name('admin_room_gallery');
   Route::post('/admin/room/gallery/store/{id}', [AdminRoomController::class, 'gallery_store'])->name('admin_room_gallery_store');
   Route::get('/admin/room/gallery/delete/{id}', [AdminRoomController::class, 'gallery_delete'])->name('admin_room_gallery_delete');










});