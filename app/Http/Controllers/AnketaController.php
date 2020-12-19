<?php
namespace App\Http\Controllers;


use App\Models\Anketa;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Metro;
use App\Models\Photo;
use App\Models\Type;
use App\Models\City;
use App\Models\Service;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class AnketaController extends Controller{

    public function allData(Request $request) {

       
        // for ($i=0; $i < 10; $i++) { 
        //     echo "<br>";
        // }

        $params = [];
        $conditions = [];
        $param_list = ['city_id', 'type_id', 'experience_id', 'education_id'];

        foreach ($param_list as $param) {
           if ($request->input($param)) {
                $params[$param] = $request->input($param);
                array_push($conditions,  [$param, '=', $request->input($param)]);
            }
        }

        // TODO find by price
        // if ($request->input('price_min')) {
        //     $params['city_id'] = $request->input('city_id');
        //     array_push($conditions,  ['price_from_bd', '>', $request->input('price_min')]);
        // }


        // TODO sort by different values
        
        return view('index', [
            'anketas' => Anketa::where($conditions)->get(), 
            'cities' => City::all(),
            'types' => Type::all(),
            'list_experience' => Experience::all(),
            'educations' => Education::all(),
            'params' => $params

        ]);
    }

    public function showAnketa($id){
        return view('anketa', ['anketa' => Anketa::getAnketaById($id)]);
    }

    public function createAnketa(){
        return view('anketa_create', [            
            'cities' => City::all(),
            'types' => Type::all(),
            'experiences' => Experience::all(),
            'educations' => Education::all(),
            'metros' => Metro::all(),
            'services' => Service::all()
        ]);
    }

    public function updateAnketa(Request $request,$id){

        $anketa= Anketa::find($id);

        $anketa->name=$request->input('profil_name');

        $anketa->age=$request->input('age');

        $anketa->about_me=$request->input('about_me');

        $anketa->price_1h_office=$request->input('price_1h_office');

        $anketa->price_2h_office=$request->input('price_2h_office');

        $anketa->price_1h_challenge=$request->input('price_1h_challenge');

        $anketa->price_2h_challenge=$request->input('price_2h_challenge');

        $type = Type::find($request->input('type'));
        $anketa->type()->associate($type);


        $anketa->tel=$request->input('tel');
        $city = City::find($request->input('id_city'));
        $anketa->city()->associate($city);
        $education = Education::find($request->input('id_education'));
        $anketa->education()->associate($education);
        $experience = Experience::find($request->input('id_experience'));
        $anketa->experience()->associate($experience);
        $anketa->metros()->detach();
        $anketa->save();

        foreach ($request->id_metros as $id_metro){         
            if($id_metro!=0){
                $anketa->metros()->attach($id_metro);               
            }
        }
        

//        if(!empty($request->photos)){
//            $i=1;
//            foreach ($request->photos as $photo) {
//                $extensionContent = $photo->getClientOriginalExtension();
//                $photo_db=new Photo();
//                $photo_db->path=$anketa->id .'_' . $i . '.' . $extensionContent;
//                $anketa->photos()->save($photo_db);
//                $photo_db->save();
//                if($i==1){
//                    $id_main_photo=$photo_db->id;
//                }
//                $path = $photo->storeAs('images', $photo_db->path , 'public');
//                $i++;
//            }
//            $anketa->photo_id=$id_main_photo;
//        }

        $anketa->save();
        return redirect()->route('home');
    }

    public function storeAnketa(Request $request){
        $anketa=new Anketa();

        $anketa->name=$request->input('profil_name');

        $anketa->age=$request->input('age');

        $anketa->about_me=$request->input('about_me');

        $anketa->price_1h_office=$request->input('price_1h_office');

        $anketa->price_2h_office=$request->input('price_2h_office');

        $anketa->price_1h_challenge=$request->input('price_1h_challenge');

        $anketa->price_2h_challenge=$request->input('price_2h_challenge');

        $type = Type::find($request->input('type'));
        $anketa->type()->associate($type);

        $user=Auth::user();
        $anketa->user()->associate($user);

        $anketa->tel=$request->input('tel');
        $city = City::find($request->input('id_city'));
        $anketa->city()->associate($city);
        $education = Education::find($request->input('id_education'));
        $anketa->education()->associate($education);
        $experience = Experience::find($request->input('id_experience'));
        $anketa->experience()->associate($experience);
        $anketa->save();

        foreach ($request->id_metros as $id_metro){
            if($id_metro!=0){
                $metro = Metro::find($id_metro);
                $metro->anketas()->attach($anketa->id);
            }
        }
        foreach ($request->services as $service_id){           
            $service = Service::find($service_id);
            $service->anketas()->attach($anketa->id);  
        }

        if(!empty($request->photos)){
            $i=1;
            foreach ($request->photos as $photo) {
                $extensionContent = $photo->getClientOriginalExtension();
                $photo_db=new Photo();
                $photo_db->path=$anketa->id .'_' . $i . '.' . $extensionContent;
                $anketa->photos()->save($photo_db);
                $photo_db->save();
                if($i==1){
                    $id_main_photo=$photo_db->id;
                }
                $path = $photo->storeAs('images', $photo_db->path , 'public');
                $i++;
            }
            $anketa->photo_id=$id_main_photo;
        }

        $anketa->save();
        return redirect()->route('home');
    }


    public function editAnketa($id){
        return view('anketa_edit', ['anketa' => Anketa::getAnketaById($id)]);
    }
}
