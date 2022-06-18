<?php
    $show = "";
    for($i = 0; $i < count($arr); $i++){
        $show = $show . '<tr>
                            <th scope = "row">' . ($i+1) . '</th>
                            <td></td>' .
                            '<td>' . $name . '</td>' . 
                            '<td>' . $price . '</td>' . 
                            /* other attrs*/
                            '<td><button type="button" class="btn btn-info" data-toggle="modal" data-target="edit-"' . $name . '>Edit </button></td>' . 
                            '<div class="modal fade" id="edit-"' . $name .  'data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">' .
                            /* modal setting */
                            '<div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal", id = "' . $name . '-edgt>Edit</button>' .
                            '</div>' .
                            /* </div>s */
                            '<script>
                                $("#' . $name . '").click( function(){
                                    $.ajax({
                                        type: "GET",
                                        url: "edit",
                                        data: {
                                            "name": $name
                                        }
                                        success: function(){
                                            window.location.replace("../home_page/home.php");
                                        }
                                    })
                                })'.
                            '</script>'
                            ;
    }
    
    echo $show

?>