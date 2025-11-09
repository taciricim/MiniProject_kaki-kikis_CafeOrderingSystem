<?php
require_once "cafe.php";

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"]);
  $review = trim($_POST["review"]);
  $rating = intval($_POST["rating"]);

  if ($name && $review && $rating) {
    $stmt = $conn->prepare("INSERT INTO reviews (name, review, rating) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $review, $rating);
    $stmt->execute();
  }
}

$reviews = $conn->query("SELECT * FROM reviews ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>customer reviews | ngopi grounds.</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f5e6d3, #fdfdfd);
      color: #3a2e25;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 900px;
      margin: 60px auto;
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      padding: 40px 50px;
    }

    h2 {
      text-align: center;
      color: #5a3e36;
      font-weight: 600;
      margin-bottom: 25px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
      background: #eee3e1;
      padding: 25px;
      border-radius: 15px;
      border: 1px solid #f0e2d0;
    }

    input, textarea, select {
      width: 100%;
      padding: 12px 15px;
      border-radius: 10px;
      border: 1px solid #b49f9fff;
      background: #ffffff;
      font-family: inherit;
      font-size: 14px;
      resize: none;
      transition: all 0.3s ease;
    }

    input:focus, textarea:focus, select:focus {
      border-color: #7c1c1c;
      box-shadow: 0 0 0 2px rgba(163,111,81,0.2);
      outline: none;
    }

    button {
      background: #5d0f0fff;
      color: white;
      border: none;
      padding: 12px 15px;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 500;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #7d2626ff;
    }

    .review-section {
      margin-top: 40px;
    }

    .review-card {
      background: #fff9f4;
      border-radius: 15px;
      padding: 20px 25px;
      margin-bottom: 20px;
      border: 1px solid #f0e2d0;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      word-wrap: break-word;
      overflow-wrap: break-word;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .review-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .review-card h3 {
      margin: 0 0 8px;
      color: #5a3e36;
    }

    .rating {
      color: #f5a623;
      margin-bottom: 10px;
    }

    .review-card p {
      font-size: 14px;
      line-height: 1.6;
      color: #4a403a;
    }

    .back-btn {
      display: inline-block;
      margin-top: 30px;
      text-decoration: none;
      color: #5a3e36;
      background: #f5e6d3;
      padding: 10px 18px;
      border-radius: 10px;
      transition: 0.3s;
      font-weight: 500;
    }

    .back-btn:hover {
      background: #e4cfb3;
    }

    .no-reviews {
      text-align: center;
      color: #85796f;
      font-style: italic;
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2><i class='bx bx-coffee'></i> Share Your Thoughts</h2>
  <form method="POST">
    <input type="text" name="name" placeholder="Your name" required>
    <textarea name="review" rows="4" placeholder="How was your coffee today?" required></textarea>
    <select name="rating" required>
      <option value="">Select Rating</option>
      <option value="5">★★★★★ (5 - Excellent)</option>
      <option value="4">★★★★☆ (4 - Good)</option>
      <option value="3">★★★☆☆ (3 - Average)</option>
      <option value="2">★★☆☆☆ (2 - Poor)</option>
      <option value="1">★☆☆☆☆ (1 - Bad)</option>
    </select>
    <button type="submit"><i class='bx bx-send'></i> Submit Review</button>
  </form>

  <div class="review-section">
    <h2><i class='bx bx-message-dots'></i> Customer Reviews</h2>

    <?php if (empty($reviews)): ?>
      <p class="no-reviews">No reviews yet — be the first to share your experience ☕</p>
    <?php else: ?>
      <?php foreach ($reviews as $r): ?>
        <div class="review-card">
          <h3><?= htmlspecialchars($r['name']) ?></h3>
          <div class="rating"><?= str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']) ?></div>
          <p><?= nl2br(htmlspecialchars($r['review'])) ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <a href="index.php" class="back-btn"><i class='bx bx-left-arrow-alt'></i> Back to Home</a>
</div>

</body>
</html>
