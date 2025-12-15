<?php require_once 'config.php'; require_once 'templates/header.php'; ?>

<div class="container mt-4">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-uppercase" style="color: var(--pl-purple); border-bottom: 4px solid var(--pl-green); display:inline-block; padding-bottom: 5px;">
            Bảng Xếp Hạng
        </h3>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-pl mb-0 align-middle">
                    <thead class="bg-light">
                        <tr style="font-size: 0.85rem; text-transform: uppercase; color: #666;">
                            <th class="text-center py-3">#</th>
                            <th class="py-3">Câu lạc bộ</th>
                            <th class="text-center py-3">ĐĐ</th>
                            <th class="text-center py-3">T</th>
                            <th class="text-center py-3">H</th>
                            <th class="text-center py-3">B</th>
                            <th class="text-center py-3">BT</th>
                            <th class="text-center py-3">BB</th>
                            <th class="text-center py-3">HS</th>
                            <th class="text-center py-3 points-col" style="background-color: #f9f9f9;">Điểm</th>
                            <th class="text-center py-3">5 trận gần nhất</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 1. KHỞI TẠO
                        $teams = [];
                        foreach ($pdo->query("SELECT * FROM teams") as $t) {
                            $teams[$t['id']] = [
                                'info' => $t, 'p' => 0, 'w' => 0, 'd' => 0, 'l' => 0, 
                                'gf' => 0, 'ga' => 0, 'pts' => 0, 'history' => [] 
                            ];
                        }

                        // 2. XỬ LÝ TRẬN ĐẤU
                        $sql = "SELECT * FROM matches WHERE finished = 1 ORDER BY match_date ASC";
                        $matches = $pdo->query($sql);

                        foreach ($matches as $m) {
                            $h = $m['home_team_id'];
                            $a = $m['away_team_id'];
                            
                            if (!isset($teams[$h]) || !isset($teams[$a])) continue;

                            // Cộng chỉ số
                            $teams[$h]['p']++; $teams[$a]['p']++;
                            $teams[$h]['gf'] += $m['home_score']; $teams[$h]['ga'] += $m['away_score'];
                            $teams[$a]['gf'] += $m['away_score']; $teams[$a]['ga'] += $m['home_score'];

                            // Thông tin trận đấu cho Tooltip
                            $score = "{$m['home_score']}-{$m['away_score']}";
                            $h_name = $teams[$h]['info']['name'];
                            $a_name = $teams[$a]['info']['name'];

                            // Logic Thắng/Thua & Lưu Lịch sử chi tiết
                            if ($m['home_score'] > $m['away_score']) {
                                $teams[$h]['w']++; $teams[$h]['pts'] += 3; 
                                $teams[$a]['l']++;
                                // Lưu mảng thay vì ký tự đơn: [Kết quả, Chi tiết hiển thị]
                                $teams[$h]['history'][] = ['res' => 'W', 'tip' => "vs $a_name ($score)"];
                                $teams[$a]['history'][] = ['res' => 'L', 'tip' => "vs $h_name ($score)"];
                            } elseif ($m['home_score'] < $m['away_score']) {
                                $teams[$a]['w']++; $teams[$a]['pts'] += 3; 
                                $teams[$h]['l']++;
                                $teams[$h]['history'][] = ['res' => 'L', 'tip' => "vs $a_name ($score)"];
                                $teams[$a]['history'][] = ['res' => 'W', 'tip' => "vs $h_name ($score)"];
                            } else {
                                $teams[$h]['d']++; $teams[$h]['pts'] += 1; 
                                $teams[$a]['d']++; $teams[$a]['pts'] += 1;
                                $teams[$h]['history'][] = ['res' => 'D', 'tip' => "vs $a_name ($score)"];
                                $teams[$a]['history'][] = ['res' => 'D', 'tip' => "vs $h_name ($score)"];
                            }
                        }

                        // 3. SẮP XẾP
                        usort($teams, function($a, $b) {
                            if ($a['pts'] != $b['pts']) return $b['pts'] - $a['pts'];
                            if (($a['gf'] - $a['ga']) != ($b['gf'] - $b['ga'])) return ($b['gf'] - $b['ga']) - ($a['gf'] - $a['ga']);
                            return $b['gf'] - $a['gf'];
                        });

                        // 4. HIỂN THỊ
                        $rank = 1;
                        foreach ($teams as $id => $data) {
                            $gd = $data['gf'] - $data['ga'];
                            
                            // Logo
                            $logo_path = $data['info']['logo'];
                            $logo_html = (!empty($logo_path) && (file_exists($logo_path) || strpos($logo_path, 'http') === 0)) 
                                ? '<img src="'.$logo_path.'" class="me-3" style="width:30px; height:30px; object-fit:contain;">'
                                : '<div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center me-3 border" style="width:30px; height:30px; font-size:12px; font-weight:bold;">'.mb_substr($data['info']['name'], 0, 1).'</div>';

                            $recent_form = array_slice($data['history'], -5);
                            ?>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td class="text-center fw-bold text-secondary"><?php echo $rank; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php echo $logo_html; ?>
                                        <a href="team_detail.php?id=<?php echo $data['info']['id']; ?>" class="text-decoration-none text-dark fw-bold team-name-hover">
                                            <?php echo $data['info']['name']; ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-center"><?php echo $data['p']; ?></td>
                                <td class="text-center"><?php echo $data['w']; ?></td>
                                <td class="text-center"><?php echo $data['d']; ?></td>
                                <td class="text-center"><?php echo $data['l']; ?></td>
                                <td class="text-center"><?php echo $data['gf']; ?></td>
                                <td class="text-center"><?php echo $data['ga']; ?></td>
                                <td class="text-center fw-bold"><?php echo ($gd > 0 ? "+$gd" : $gd); ?></td>
                                <td class="text-center fw-black points-col" style="font-size: 1.1rem; color: #000; background-color: #f9f9f9;"><?php echo $data['pts']; ?></td>
                                
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <?php foreach ($recent_form as $item): ?>
                                            <?php 
                                                if($item['res'] == 'W') { $cls = 'form-win'; } 
                                                elseif($item['res'] == 'D') { $cls = 'form-draw'; } 
                                                else { $cls = 'form-loss'; }
                                            ?>
                                            <div class="badge-container">
                                                <span class="form-badge <?php echo $cls; ?>">
                                                    <?php echo $item['res']; ?>
                                                </span>
                                                <div class="custom-tooltip">
                                                    <?php echo $item['tip']; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        
                                        <?php for($k=0; $k < (5 - count($recent_form)); $k++): ?>
                                            <span class="form-badge form-empty"></span>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php $rank++; } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3 text-muted small p-2">
            <span class="me-3"><span class="form-badge form-win d-inline-flex" style="width:15px; height:15px; font-size:8px;">W</span> Thắng</span>
            <span class="me-3"><span class="form-badge form-draw d-inline-flex" style="width:15px; height:15px; font-size:8px;">D</span> Hòa</span>
            <span><span class="form-badge form-loss d-inline-flex" style="width:15px; height:15px; font-size:8px;">L</span> Thua</span>
        </div>
    </div>
</div>

<style>
    /* Badge cơ bản */
    .form-badge { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 4px; color: #fff; font-size: 11px; font-weight: 800; cursor: pointer; }
    .form-win { background-color: #13cf00; } .form-draw { background-color: #888888; } .form-loss { background-color: #d81920; } .form-empty { background-color: #f2f2f2; border: 1px solid #ddd; margin: 0 2px; }

    /* --- CSS TOOLTIP HIỆU ỨNG --- */
    .badge-container {
        position: relative; /* Để tooltip định vị theo ô này */
        margin: 0 2px;
        display: inline-block;
    }

    .custom-tooltip {
        visibility: hidden;
        width: 140px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;
        
        /* Định vị tooltip nằm trên đầu */
        position: absolute;
        z-index: 1000; /* Đảm bảo nằm trên cùng */
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%); /* Căn giữa */
        
        /* Hiệu ứng mờ */
        opacity: 0;
        transition: opacity 0.3s, visibility 0.3s;
        
        font-size: 11px;
        font-weight: normal;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    /* Mũi tên nhỏ dưới tooltip */
    .custom-tooltip::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
    }

    /* Khi di chuột vào thì hiện lên */
    .badge-container:hover .custom-tooltip {
        visibility: visible;
        opacity: 1;
    }

    .team-link-hover:hover span { color: var(--pl-green) !important; text-decoration: underline; }
</style>

</body></html>