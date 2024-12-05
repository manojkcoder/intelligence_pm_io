<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('GPT') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg py-4 px-4">
                <!-- Textarea for user input -->
                <label for="prompt" class="font-bold">Prompt:</label>
                <br>
                <textarea id="prompt" name="prompt" rows="4" cols="50" class="bg-white w-100 font-bold py-2 px-4 rounded"></textarea>
                <!-- Button to submit user input -->
                 <br>
                <button id="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" type="button">Submit</button>
                <!-- Div to display the response from the server -->
                <div id="response" class="font-bold py-2 px-4 rounded"></div>
            </div>
        </div>
    </div>
    </div>
    <script>
        $(document).ready(function(){
            // When the submit button is clicked
            $('#submit').click(function(){
                $('#response').html('Loading...');
                // Get the user input
                var prompt = $('#prompt').val();
                // Send the user input to the server, including the CSRF token
                $.post('{{ route("prompt") }}', {_token: '{{ csrf_token() }}', prompt: prompt}, function(data){
                    // Display the response from the server
                    $('#response').html(data);
                });
            });
        });
    </script>
</x-app-layout>