<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Post;

class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // as views que podem ser exibidas mesmo sem autenticação
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $data = Post::all();
        // $data = Post::where('title', 'Post Two')->get();
        // $data = DB::select('SELECT * FROM posts');
        // $data = Post::orderBy('created_at', 'desc')->take(1)->get();
        // $data = Post::orderBy('created_at', 'desc')->get();

        // Pega todos os posts do ultimo ao primeiro e coloca paginação de 10/pagina
        $data = Post::orderBy('created_at', 'desc')->paginate(10);                        
        return view('posts.index')->with('posts', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'body'  => 'required',
            'cover_image' => 'image|nullable|max:1999'
        ],[
            'title.required' => 'Campo título é obrigatório.',
            'body.required'  => 'Campo texto é obrigatório',
            'cover_image.image' => 'Selecione um arquivo de imagem',
            'cover_image.max' => 'Tamanho máximo excedido. Max: 2Mb',
        ]);

        if($request->hasFile('cover_image')){
            // Pega o nome do arquivo com a extensão
            $fileNameWithExtension = $request->file('cover_image')->getClientOriginalName();
            // Pega só o nome do arquivo
            $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            // Pega só a extensão
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // Cria um nome único com um timestamp para guardar
            $fileNameToStore = $fileName.'_'.time().'.'.$extension;
            // Faz o upload
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        } else {
            $fileNameToStore = 'noimage.png';
        }
        
        $post = new Post();
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = auth()->user()->id;
        $post->cover_image = $fileNameToStore;
        $post->save();

        return redirect('/posts')->with('success', 'Post criado');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Post::find($id);
        return view('posts.show')->with('post', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);

        // Verificando o usuário
        if(auth()->user()->id !== $post->user_id){
            return redirect('/posts')->with('error', 'Acesso negado!');
        }

        return view('posts.edit')->with('post', $post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'body'  => 'required'
        ],[
            'title.required' => 'Campo título é obrigatório.',
            'body.required'  => 'Campo texto é obrigatório'
        ]);
        
        if($request->hasFile('cover_image')){
            // Pega o nome do arquivo com a extensão
            $fileNameWithExtension = $request->file('cover_image')->getClientOriginalName();
            // Pega só o nome do arquivo
            $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            // Pega só a extensão
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // Cria um nome único com um timestamp para guardar
            $fileNameToStore = $fileName.'_'.time().'.'.$extension;
            // Faz o upload
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        }

        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        if($request->hasFile('cover_image')){
            $post->cover_image = $fileNameToStore;
        }
        $post->save();

        return redirect('/posts')->with('success', 'Post editado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        // Verificando o usuário
        if(auth()->user()->id !== $post->user_id){
            return redirect('/posts')->with('error', 'Acesso negado!');
        }

        if($post->cover_image != 'noimage.png'){
            // Deleta a imagem do storage
            Storage::delete('/public/cover_images/'.$post->cover_image);
        }

        $post->delete();
        return redirect('/posts')->with('success', 'Post excluido');
    }
}
