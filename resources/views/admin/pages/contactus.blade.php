@extends('admin.master')
@section('content')
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $('.delbanner').click(function(e){

            if(confirm("Are you sure you want to delete this?")){
                var cid=$(this).attr("cid");
                $.ajax({
                    url:"banners/"+cid,
                    type:'delete',
                    data:{_token:'{{csrf_token()}}',cid:cid},
                    success:function(response){
                     
                        $("#mytable").load(location.href + " #mytable");
                    }
                });
            }
    else{
        return false;
    }   
        });
    });
</script> -->
<div class="content-wrapper">
<div class="container mt-5 ">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg">
                <div class="text-center">
                    <a href="#" class="btn btn-success ml-5 mt-5">Show Query</a>
                </div>
                <div class="card-body">
                    <table class="table text-white" id="mytable">
                        <thead>
                            <tr>
                                <th scope="col">S.no</th>
                                <th scope="col"> Name</th>
                                <th scope="col"> Email</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sn=1;
                            @endphp
                            @foreach($contactuss as $contactus)
                                <tr>
                                    <td>{{$sn}}</td>
                                    <td>{{$contactus->name}}</td>
                                    <td>{{$contactus->email}}</td>
                                    <td>{{$contactus->subject}}</td>
                                    <td>{{$contactus->message}}</td>

                                   

                                 
                                </tr>
                                @php
                                    $sn++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                 
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
<style>
    .w-5{
        display:none;
    }
</style>