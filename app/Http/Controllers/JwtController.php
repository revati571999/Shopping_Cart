<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\OrderDetails;
use App\Models\Order;
use App\Models\CMS;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\ContactUs;
use Illuminate\Support\Facades\Hash;
use App\Mail\registermail;
use App\Mail\ordermail;
use App\Models\Coupon;
use App\Http\Resources\UserApiResource;
use Illuminate\Support\Facades\Mail; 


class JwtController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['login','register','product','contactus','banner','category','show','checkout','CMSDetails','MyOrder','Coupons']]);
    }
    public function register(Request $request){
        $validator=Validator::make($request->all(),[
            'firstname'=>'required|min:2|alpha',
            'lastname'=>'required|min:2|alpha',
            'email'=>'required|unique:users|email',
            'password'=>'required|min:6|max:12',
            'cpassword'=>'required|min:6|max:12|required_with:password|same:password',
        ]);
        Mail::to($request->email)->send(new registermail($request->all()));
       
        if($validator->fails()){
            return response()->json($validator->errors());
        }
        else {
            $user=User::create([
                'firstname'=>$request->firstname,
                'lastname'=>$request->lastname,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
                'status'=>$request->status ?? '1',
                'role_id' => $request->role ?? '5',
            ]);
            return response()->json([
                'message'=>'User create successfully',
                'user'=>$user
            ],201);
        }
    }
    // public function login(Request $request){
    //     $validator=Validator::make($request->all(),[
    //         'email'=>'required|email',
    //         'password'=>'required|min:6|max:12',
    //     ]);
    //     if($validator->fails()){
    //         return response()->json($validator->errors());
    //     }
    //     else {
    //         $token=auth()->attempt($validator->validated());
            
    //         if(!empty($token)){
    //            return response()->json(['error'=>0,'token'=>$token,'email'=>$request->email]);
    //         }
    //         // return $this->respondWithToken($token);
    //         return response()->json(['error'=>0,'token'=>$token,'email'=>$request->email]);
    //     }
    // }
    // public function login(Request $request){
    //     $validator=Validator::make($request->all(),[
    //         'email'=>'required|email',
    //         'password'=>'required|min:6|max:12',
    //     ]);
    //     if($validator->fails()){
    //         return response()->json($validator->errors());
    //     }
    //     else {
    //         if(!$token=auth()->attempt($validator->validated())){
    //            return response()->json(['error'=>'Unauthorized','token'=>$token],401);
    //         }
    //         // return $this->respondWithToken($token);
    //         return response()->json(['error'=>0,'token'=>$token,'email'=>$request->email],200);
    //     }
    // }
    public function login(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required|min:6|max:12',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors());
        }
        else {
            $user=User::where('email',$request->email)->first();
            // if(!$token=auth()->guard('api')->attempt($validator->validated())){
            //    return response()->json(['error'=>'Unauthorized','token'=>$token],401);
            // }
            // // return $this->respondWithToken($token);
            // return response()->json(['error'=>0,'token'=>$token,'email'=>$request->email],200);

            if ($user->status ==1) {
                if(!$token=auth()->attempt($validator->validated())){
                return response()->json(['token' => $token,'error'=>0 ,'message' => 'Login successfully.', 'status' => 1, 'email'=>$request->email],200);
            }
        }
           else {
                 return response()->json(['token' => '','error'=>0, 'message' => 'User is inactive.', 'status' => 0]);
                }
        }
        return response()->json(['token' => $token,'error'=>0 ,'message' => 'Login successfully.', 'status' => 1, 'email'=>$request->email],200);
          
    }
    public function contactus(Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required|min:2|alpha',
            'email'=>'required|email',
            'subject'=>'required',
            'message'=>'required|min:6',
          
        ]);
        if($validator->fails()){
            return response()->json($validator->errors());
        }
        else {
            $contactus=ContactUs::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'subject'=>$request->subject,
                'message'=>$request->message,
               
            ]);
            return response()->json([
                'error'=>1,
                'contact'=>$contactus
            ],201);
        }
    }
    public function banner()
    {
        $banner = Banner::all();
        foreach($banner as $ban){
            $listbanner[]=[
                'caption'=>$ban->caption,
                'image'=> asset('uploads/'.$ban->image)
              ];
          }
 
        return response()->json(['banner' => $listbanner]);
    }
   

    public function category()
    {
        $category = Category::all();
        foreach($category as $cat){
            $listcat[]=[
                'id'=>$cat->id,
                'name'=>$cat->name,
                'description'=>$cat->description,
              ];
          }
 
        return response()->json(['category' => $listcat]);
    }
    public function product()
    {
        $products = Product::with('ProductImage','ProductAttributeAssoc')->get();
        return response()->json(['products' => $products]);
        // $list=[];
        // $listimage=[];
        // $product = Product::all();
        // foreach($product as $prod){
        //   foreach($prod->ProductImage as $image){
        //         $listimage[]=[
        //             'image'=> asset('uploads/'.$image->images)
        //           ];
        //   }       
        //     $list[]=[
        //         'name'=>$prod->pname,
        //         'pid'=>$prod->id,
        //         'category'=>$prod->ProductCategory,
        //         'attributes'=>$prod->ProductAttributeAssoc,
        //         'images'=>$listimage,
        //     ];
        //     $listimage=[];
        // }
    
        // return response()->json(['product' => $list]);
    }
    public function show($id)
    {
        $list = [];
        $product = Product::join('product_categories','products.id','=','product_categories.products_id')->where('product_categories.categories_id',$id)->get();
        foreach ($product as $prod) {
            foreach($prod->ProductImage as $image){
                $listimage[]=[
                    'image'=> asset('uploads/'.$image->images)
                  ];
          }
            $list[] = [
                'name' => $prod->pname,
                'pid' => $prod->id,
                'category'=>$prod->ProductCategory,
                'attributes'=>$prod->ProductAttributeAssoc,
                'images'=>$listimage,
            ];
            $listimage = [];
        }

        return response()->json(['categorybyid' => $list]);
    }

    public function get_user(Request $request)
    {
        $user = User::latest()->get();
 
        return response()->json(['user' => $user]);
    }
    public function logout(){
        auth()->logout();
        return response()->json(["message"=>"User Logout Successfully"]);
    }
    public function respondWithToken($token){
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL()*60
        ]);
    }
    public function profile(){
        $profile=auth('api')->user();
         return response()->json(['profile'=>$profile]);
     }
     public function changePassword(Request $request){


        $validator = Validator::make($request->all(), [
            'oldpass'=>'required',
            'newpass'=>'required|min:6|max:100',
            'confirmpass'=>'required|same:newpass'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'Validations fails',
                'error'=>$validator->errors()
            ],422);
        }

        $user=User::where('email',$request->email)->first();
        if(Hash::check($request->oldpass,$user->password)){
            $user->update([
                'password'=>Hash::make($request->newpass)
            ]);
            return response()->json([
                'message'=>'Password successfully updated',
            ],200);
        }else{
            return response()->json([
                'message'=>'Old password does not matched'
            ],400);
        }

    }
    //
    public function CMSDetails()
    {
        $services = CMS::all();
        foreach($services as $c){
            $listbanner[]=[
                'title'=>$c->title,
                'description'=>$c->description,
                'image'=> asset('/cms'.$c->image)
              ];
          }
 
        return response()->json(['services' => $listbanner]);
    }
    public function refresh(){
        return $this->responseWithToken(auth()->refresh());
    }
    public function checkout(Request $req){
        // return response()->json($req->all());
        $uemail = $req->email;

        $user = User::where('email',$uemail)->first();
        $userdetails = new UserDetails();
        $userdetails->user_id = $user->id;
        $userdetails->email = $req->email;
        $userdetails->firstname = $req->firstname;
        $userdetails->lastname = $req->lastname;
        $userdetails->address1 = $req->address1;
        $userdetails->zip = $req->zip;
        $userdetails->phone = $req->phone;
        $userdetails->shipping = $req->shipping;
        $userdetails->save();

        $userdetail = UserDetails::latest()->first();

        $orders = $req->product;
    
            foreach($orders as $ord)
            {
                $order = new Order();
                $order->userdetail_id = $userdetail->id;
                $order->product_id = $ord['pid'];
                $order->save();

                
                $orderdetail = new OrderDetails();
                $orderdetail->userdetail_id = $userdetail->id;
                $orderdetail->order_id = $order->id;
                $orderdetail->producttotal = $req->producttotal;
                $orderdetail->finalTotal = $req->finalTotal;
                $orderdetail->coupon_id =$req->coupon;
                $orderdetail->save();


            }
           
            
        
            Mail::to($req->email)->send(new ordermail($req->all()));
        return response()->json(['msg'=>"Order Placed Successfully !"],200);
    }
    public function Coupons(){
        $coupon = Coupon::where('status','1')->get();
        return response(['coupons'=>UserApiResource::collection($coupon)]);
    }


    // public function changepass(Request $request){
    //     $validator=Validator::make($request->all(),[
    //         'old_password'=>'required|min:6|max:12',
    //         'new_password'=>'required|min:6|max:12',
    //         'confirm_password'=>'required|min:6|max:12|same:new_password',

    //     ]);
    //     if($validator->fails()){
    //         return response()->json($validator->errors());
    //     }
    //     else {
    //         $user=$request->user();
    //         if(Hash::check($request->old_password,$user->password)){
    //            $user->update([
    //                'password'=>Hash::make($request->new_password)
    //            ]);
    //            return response()->json([
    //             'message'=>"password successfully updated",
    //             'status'=>1
    //             ],200);
    //         }
    //        else{
    //             return response()->json([
    //                 'message'=>"old password does not match",
    //             ],400);
    //        }
    //     }
    //     return response()->json([
    //         'message'=>"password successfully updated",
    //         'status'=>1
    //     ]);
    // }
    // public function updateProfile(Request $request){
    //     $validator=Validator::make($request->all(),[
    //         'firstname'=>'required|min:2|alpha',
    //         'lastname'=>'required|min:2|alpha',
    //         'email'=>'required|unique:users|email',
    //     ]);
    //     if($validator->fails()){
    //         return response()->json($validator->errors());
    //     }
    //     else {
    //         $user=User::find($request->user()->id);
    //             $user->firstname=$request->firstname;
    //             $user->lastname=$request->lastname;
    //             $user->email=$request->email;
    //             $user->update();
    //         return response()->json([
    //             'message'=>"profile updated successfully",
    //             'updatedprofile'=>$user
    //         ]);
    //     }
    //      return response()->json(['status'=>1,'updatedprofile'=>$user]);
    //  }

    //  public function productt()
    // {
        
    //     $products = Product::with('ProductImage','ProductAttributeAssoc')->get();
    //     return response()->json(['products' => $products]);



        // foreach($product as $prod){
        //   foreach($prod->image as $image){
        //         $listimage[]=[
        //             'imagepath'=> asset('productimages/'.$image->imagepath)
        //           ];
        //   }       
        //     $list[]=[
        //         'name'=>$prod->name,
        //         'pid'=>$prod->id,
        //         'category'=>$prod->productcategory,
        //         'attributes'=>$prod->attribute,
        //         'imagepath'=>$listimage,
        //     ];
        //     $listimage=[];
        // }
    
    // }


    //
    // public function MyOrder($id){
    //     $userdetail = UserDetails::where('user_id',$id)->get();
    //     $orderdetail = OrderDetails::all();
    //     $orders = Order::all();
    //     $orderdetails = ["userdetail"=>$userdetail,"orderdetail"=>$orderdetail,"orders"=>$orders];
    //     return response()->json($orderdetails);
    // }
   
   
    public function MyOrder($id){
        $userdetail = UserDetails::where('user_id',$id)->get();
        $orderdetail = OrderDetails::all();
        $orders = Order::all();
        $orderdetails = ["userdetail"=>$userdetail,"orderdetail"=>$orderdetail,"orders"=>$orders];
        return response()->json($orderdetails);
    }
   
}