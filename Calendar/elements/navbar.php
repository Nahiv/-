<header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php"><?= SITE_NAME ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="add.php"><i class="fa fa-plus"></i>追加</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="search.php"><i class="fa fa-search"></i>検索</a>
                        </li>
                    </ul>
                    <form class="d-flex ym-flex" action="index.php">
                        <label class="sp-label" for="inputYmPicker">年月を選択</label>
                        <input type="text" name="ym" class="form-control me-2" placeholder="年月を選択" id="ymPicker">
                        <button class="btn btn-outline-light text-nowrap" type="submit">表示</button>
                    </form>
                </div>
            </div>
        </nav>
</header>