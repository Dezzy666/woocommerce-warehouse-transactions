<style>
    .insertion {
        width: 90%;
        background-color: white;
        padding: 5px 20px 20px 20px;
        border: 3px solid black;
        border-radius: 10px;
        margin-top: 10px;
        margin-bottom: 5px;
    }

    .disabled {
        opacity: .5;
    }

    .product-select {
        width: 350px;
    }

    .recent .product-name {
        width: 250px;
        text-align: left;
    }

    .recent .last .newer{
        text-align: left;
    }

    .recent .last .older {
        text-align: right;
    }

    .recent .last .newer input, .recent .last .older input {
        width: 100px;
        border: 2px solid black;
        background-color: #87b5ff;
        border-radius: 3px;
        cursor: pointer;
    }

    .recent .last .newer input:hover, .recent .last .older input:hover {
        background-color: #5a98fc;
    }

    .recent .last .newer input:active, .recent .last .older input:active {
        background-color: #59fc6c;
    }

    .recent {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .recent tr:nth-child(2n) {
        background-color: lightgray;
    }

    span.select2-container {
        display: inline-block !important;
    }
</style>