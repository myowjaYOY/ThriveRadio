class CategoryModel {
  const CategoryModel({
    required this.categoryId,
    required this.categoryName,
    required this.imageUrl,
  });

  CategoryModel.fromJson(Map<String, dynamic> json)
      : categoryId = json['id'] as int,
        categoryName = json['name'] as String,
        imageUrl = json['image'] as String;

  final int categoryId;
  final String imageUrl;
  final String categoryName;
}
