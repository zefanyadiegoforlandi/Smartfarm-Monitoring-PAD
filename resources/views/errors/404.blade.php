<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        body {
            background-color: white;
        }
        .button {
            background-color: #416D14;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            text-align: center;
            display: inline-block;
            margin-top: 1rem;
            transition: background-color 0.3s, transform 0.3s;
        }
        .button:hover {
            background-color: #355012;
        }
        .button:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="max-w-2xl m-auto mt-16">
            <div class="text-center px-4">
                <div class="inline-flex mb-8">
                    <img src="{{ asset('images/404-image.jpg') }}" class="w-64 h-64 md:w-128 md:h-128" alt="New illustration" />
                </div>
                <div class="mb-6">Hmm...this page doesn’t exist. Go back to the previous page!</div>
                <a href="javascript:history.back()" class="button">Go Back</a>
            </div>
        </div>
    </div>
</body>
</html>
