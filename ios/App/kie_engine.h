#import <Foundation/Foundation.h>

@interface KieEngine : NSObject

+ (void)initializeEngine;
+ (void)shutdownEngine;
+ (NSString *)executePHPCode:(NSString *)phpCode;
+ (NSString *)executePHPRequest:(NSString *)url method:(NSString *)method appPath:(NSString *)appPath;

@end
