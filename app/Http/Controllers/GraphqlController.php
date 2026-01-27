<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Graphql\GraphQLHandle;

class GraphqlController extends Controller
{
  //
  function __invoke(Request $request, GraphQLHandle $gql)
  {
    return response()->json(
      $gql->handle(
        // parse payload
        $request->validate([
          'query'         => 'required|string',
          'variables'     => 'nullable|array',
          'operationName' => 'nullable|string',
        ])
      )
    );
  }
}
